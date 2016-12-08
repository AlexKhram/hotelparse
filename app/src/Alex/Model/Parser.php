<?php

namespace Alex\Model;

class Parser extends Model
{
    public function parseData($url)
    {
        $doc = new \DOMDocument();
        @$doc->loadHTMLFile($url);
        if (!isset($doc->doctype)) {
            return false;
        }

        $finder = new \DomXPath($doc);

        $hotel = [];
        $name = $doc->getElementById('hp_hotel_name');
        if (isset($name->nodeValue)) {
            $hotel['name'] = trim($name->nodeValue);
        } else {
            return false;
        }

        $address = $finder->query("//span[contains(@class,'hp_address_subtitle')]");
        if (isset($address[0]->nodeValue)) {
            $hotel['address'] = trim($address[0]->nodeValue);
        } else {
            $hotel['address'] = '';
        }

        $descTitle = $doc->getElementById('summary')->getElementsByTagName('h3');
        if (isset($descTitle[0]->nodeValue)) {
            $hotel['descTitle'] = trim($descTitle[0]->nodeValue);
        } else {
            $hotel['descTitle'] = '';
        }

        $descs = $doc->getElementById('summary')->getElementsByTagName('p');
        $hotel['desc'] = '';
        foreach ($descs as $desc) {
            $hotel['desc'] .= trim($desc->nodeValue) . "\n";
        }

        $descGeo = $finder->query("//p[@class='geo_information']");
        if (isset($descGeo[0]->nodeValue)) {
            $hotel['descGeo'] = trim($descGeo[0]->nodeValue);
        } else {
            $hotel['descGeo'] = '';
        }

        $descReview = $finder->query("//p[@class='hp-desc-review-highlight']");
        if (isset($descReview[0]->nodeValue)) {
            $hotel['descReview'] = trim($descReview[0]->nodeValue);
        } else {
            $hotel['descReview'] = '';
        }

        $descLang = $finder->query("//p[@class='hp-desc-we-speak']");
        if (isset($descLang[0]->nodeValue)) {
            $hotel['descLang'] = trim($descLang[0]->nodeValue);
        } else {
            $hotel['descLang'] = '';
        }

        $idHotel = $this->app['modelHotel']->addNewHotel($hotel);

        if (!$idHotel) {
            return (['status' => 'Hotel already exist']);
        }

        $facilities = $finder->query("//div[contains(@class,'important_facility')]");
        foreach ($facilities as $facility) {
            if (!$idFacility = $this->app['modelFacility']->getIdFacilityByName(trim($facility->nodeValue))) {
                $idFacility = $this->app['modelFacility']->addNewFacility(trim($facility->nodeValue));
            }
            $this->app['modelFacility']->addFacilityForHotel($idHotel, $idFacility);
        }


        $photos = $finder->query("//div[@data-photoid]/img");
        $hotelPhotosArray = [];
        foreach ($photos as $photo) {
            if ($photo->hasAttribute('src')) {
                $hotelPhotosArray[] = $photo->getAttribute('src');
            } elseif ($photo->hasAttribute('data-lazy')) {
                $hotelPhotosArray[] = $photo->getAttribute('data-lazy');
            }
        }

        $baseImageName = str_replace(' ', '_', strtolower($hotel['name']));
        $counter = 1;
        foreach ($hotelPhotosArray as $hotelPhoto) {
            $path = "images/{$baseImageName}_{$counter}.jpg";
            $counter++;
            if (file_put_contents($path, file_get_contents($hotelPhoto))) {
                $this->app['modelImage']->addNewImageForHotel($idHotel, '/' . $path);
            }
        }

        return true;
    }

}