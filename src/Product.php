<?php

namespace App;

class Product
{
    protected string $title ='';
    protected string $price ='';
    protected string $imageUrl ='';
    protected string $capacity ='';
    protected string $colour ='';
    protected string $availabilityText ='';
    protected bool $isAvailable;
    protected string $shippingText = '';
    protected string $shippingDate = '';


    
    protected function setProductTitle($nodeElement, $nthElement) {
        $this->title = $this->checkNodeExisted($nodeElement, $nthElement);
    }

    protected function getProductTitle() {
        return $this->title;
    }
    protected function setProductPrice($nodeElement, $nthElement) {
        $this->price = $this->checkNodeExisted($nodeElement, $nthElement);

    }

    protected function getProductPrice() {
        return $this->price;
    }

    protected function setProductImageUrl($imageList) {
       $this->imageUrl = count($imageList) > 0 ? str_replace("../", "https://www.magpiehq.com/developer-challenge/", $imageList[0]) : '';
    }

    protected function getProductImageUrl() {
        return $this->imageUrl;
    }


    protected function setProductCapacity($nodeElement, $nthElement) {
        $productCapacity = $this->checkNodeExisted($nodeElement, $nthElement);
        $this->capacity = str_contains(strtoupper($productCapacity), 'GB') ? str_replace("GB", "", $productCapacity)*1000: $productCapacity;
     }
 
     protected function getProductCapacity() {
         return $this->capacity;
     }
     protected function setProductColourList($colourList) {
        $this->colour = count($colourList) > 0 ? implode(",",  $colourList) : '';
     }
     
     protected function setProductColour($colour) {
        $this->colour = $colour;
     }
 
     protected function getProductColour() {
         return $this->colour;
     }
 
     protected function setAvailabilityText($nodeElement, $nthElement) {
        $availabilityText = $this->checkNodeExisted($nodeElement, $nthElement);
        $availabilityTextList =explode(": ", $availabilityText);
        $this->availabilityText = count($availabilityTextList) > 0 ? $availabilityTextList[1] : "";   
     }
 
     protected function getAvailabilityText() {
         return $this->availabilityText;
     }


     protected function setIsAvailable($availabilityText) {
        $this->isAvailable = str_contains(strtoupper($this->availabilityText),"IN") ? true : false;
   
     }
 
     protected function getIsAvailable() {
         return $this->isAvailable;
     }

     protected function setShippingText($nodeElement, $nthElement) {
        $this->shippingText = $this->checkNodeExisted($nodeElement, $nthElement);

    }

    protected function getShippingText() {
        return $this->shippingText;
    }

    protected function setShippingDate($shippingText) {
        $this->shippingDate = $this->extractDateFromShippingText($shippingText);

    }

    protected function getShippingDate() {
        return $this->shippingDate;
    }

    
    protected function extractDateFromShippingText($shippinText) {

        if (preg_match('/[0-9+]+[,\s]*([^,]+)/', $shippinText, $matches)) {
            return $matches[0];
        }
        return '';
    
    }


     



    protected function checkNodeExisted($node, $nthElement) {
        return count($node) > $nthElement ? $node->eq($nthElement)->text() : '';
    }
    

}
