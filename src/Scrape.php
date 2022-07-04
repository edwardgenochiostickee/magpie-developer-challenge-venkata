<?php

namespace App;


require '../vendor/autoload.php';
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector;


class Scrape extends Product
{
    private array $products = [];
    private string $url = 'https://www.magpiehq.com/developer-challenge/smartphones/';
    private array $document = [];
    private object $pagesData ;
    private int $totalPages = 1;
    private int $pageNo = 1;

    public function run($url = 'https://www.magpiehq.com/developer-challenge/smartphones/'): void
    {

        $this->document[$this->pageNo] = ScrapeHelper::fetchDocument($url);
        
        if ($this->pageNo == 1) {        
            $noOfpagesContains = explode("of ", $this->document[$this->pageNo]->filter('p[class="block text-center my-8"]')->text());
            $this->totalPages = $noOfpagesContains[1];
        }

        if ($this->pageNo+1 <= $this->totalPages) {

            $this->pageNo++;
            $this->run($this->url.'?page='.$this->pageNo);
            
        } else {         
            
            $this->products = $this->getProductsFromNodes($this->document);
            try {
                file_put_contents('output.json', json_encode($this->products)); 
                echo "output.json file created successfully.";
            } catch(Exception $e) {
                echo 'Message: ' .$e->getMessage();
            }          

        }        
     
    }

    private function getProductsFromNodes($document) {

        foreach($document as $key => $CrawlerObject) {           

            $filter = 'div .product';
            $catsHTML[$key]= $CrawlerObject
                            ->filter($filter)
                            ->each(function (Crawler $node) {
                                $productColourList = $node->children('div')->eq(0)->filterXpath('//span[contains(@class, "block")]')->extract(array('data-colour'));

                                if (count($productColourList) > 0) {
                                    foreach($productColourList as $key=> $colour) {
                                        
                                        $this->setProductTitle($node->filter('.product-name'), 0);
                                        $products[$colour]['title']= $this->getProductTitle();                                   

                                        $this->setProductPrice($node->children('div')->eq(0)->filter('div.block'), 0);
                                        $products[$colour]['price']=  $this->getProductPrice();

                                        $this->setProductImageUrl($node->children('div')->eq(0)->filterXpath('//img')->extract(array('src')));
                                        $products[$colour]['imageUrl']=  $this->getProductImageUrl();

                                        $this->setProductCapacity($node->filter('.product-capacity'), 0);
                                        $products[$colour]['capacityMB']=  $this->getProductCapacity();

                                        $this->setProductColour($productColourList[0]);
                                        $products[$colour]['colour']=  $this->getProductColour();
                                        
                                        $this->setAvailabilityText($node->children('div')->eq(0)->filter('div.block'), 1);
                                        $products[$colour]['availabilityText']=  $this->getAvailabilityText();

                                        $this->setIsAvailable($products[$colour]['availabilityText']);
                                        $products[$colour]['isAvailable']=  $this->getIsAvailable();

                                        $this->setShippingText($node->children('div')->eq(0)->filter('div.block'), 2);
                                        $products[$colour]['shippingText']=  $this->getShippingText(); 

                                       $this->setShippingDate($products[$colour]['shippingText']);

                                        $products[$colour]['shippingDate'] = $this->getShippingDate();
                                    }
                                }
                                
                                return array_values($products);

                                });

        }

        // prepare single array
        $productList = array();
        $productListAr = array();       
       
        $i =0;
       
        for ($page =1; $page <= $this->pageNo ; $page++) {
            foreach($catsHTML[$page] as $key => $colourProductList) {
                foreach ($colourProductList as $productKey => $productListDetails ) {
                    $productList = array_merge($productList, $productListDetails);                    
                    $productListAr[$i] = $productList;
                    $i++;
                 
                }          
           }          
            
        }        
          
        $productsUnique = $this->array_multi_unique($productListAr);         
        return $productsUnique;  
    }

    private function array_multi_unique($multiArray){

        $uniqueArray = array();
      
        foreach($multiArray as $subArray){
      
          if(!in_array($subArray, $uniqueArray)){
            $uniqueArray[] = $subArray;
          }
        }
        return $uniqueArray;
      }



}

$scrape = new Scrape();
$scrape->run();
