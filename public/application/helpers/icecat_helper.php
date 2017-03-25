<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * =================================================================
 * Copyright (c) 2013 Roy Duineveld (royduineveld.nl)
 * =================================================================
 *
 * License
 * =================================================================
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 * =================================================================
 *
 * Notes
 * =================================================================
 * PHP 5.3 or higher is required!
 * =================================================================
 *
 * Changelog
 * =================================================================
 * Version 1.0 (19-01-2013)
 * - First release
 * Version 1.1 (22-01-2013)
 * - Two typos fixt
 * - Added ignore_errors in a stream_context_create function so
 *   file_get_contents will get the content even if it's returning
 *   a 404. So error 2 will not be triggered before error 3 get his
 *   chance. Error 3 correspond to the error Icecat is giving.
 * =================================================================
 */
 
/**
 * Read Icecat data from XML into a array
 * @param  array $data Information array
 * Information array options:
 * ==========================
 * - ean        = Product EAN
 * - sku        = Product SKU
 * - brand      = Product brand
 * - id_full    = Icecat ID (if you've full Icecat)
 * - id_free    = Icecat ID (if you're using Open-Icecat)
 * - language   = Language
 * - username   = Icecat username
 * - password   = Icecat password
 * ==========================
 * @return array       Product information
 */
function icecat_to_array($data = array())
{
    // Extract data array
    extract($data);
 
    // Check given array
    if(!isset($ean) AND !isset($sku) AND !isset($id_full) AND !isset($id_free)){ $errors[1] = 'No EAN, SKU or Icecat ID given!'; goto the_end; }
    if(isset($sku) AND !isset($brand)){ $errors[1] = 'SKU given but no brand!'; goto the_end; }
    if(!isset($language)){ $errors[1] = 'No language given!'; goto the_end; }
    if(!isset($username) OR !isset($password)){ $errors[1] = 'No username and/or password given!'; goto the_end; }
 
    // Set url
    if(isset($ean)){        $url = 'http://' . $username . ':' . $password . '@data.Icecat.biz/xml_s3/xml_server3.cgi?ean_upc=' . $ean . ';lang=' . $language . ';output=productxml'; }
    if(isset($sku)){        $url = 'http://' . $username . ':' . $password . '@data.Icecat.biz/xml_s3/xml_server3.cgi?prod_id=' . $sku . ';vendor=' . $brand . ';lang=' . $language . ';output=productxml'; }
    if(isset($id_free)){    $url = 'http://' . $username . ':' . $password . '@data.icecat.biz/export/freexml.int/' . $language . '/'.$id_free.'.xml'; }
    if(isset($id_full)){    $url = 'http://' . $username . ':' . $password . '@data.icecat.biz/export/level4/' . $language . '/'.$id_full.'.xml'; }
 
    // Get data
    $xml = @file_get_contents($url,false,stream_context_create(array('http' => array('ignore_errors' => true))));
    if(!$xml){ $errors[2] = 'Unable to download the product feed! Maybe Icecat isn\'t reachable!'; goto the_end; }
 
    // Load into Simple XML Element
    $xml = new SimpleXMLElement($xml);
 
    // Set xpaths
    $product            = $xml->xpath("/ICECAT-interface/Product");
    $product_attr       = $product[0]->attributes();
 
        // Does Icecat give errors?
        if($product_attr['ErrorMessage']){ $errors[3] = (string)$product_attr['ErrorMessage']; goto the_end; }
 
    $category           = $xml->xpath("/ICECAT-interface/Product/Category");
 
    $description        = $xml->xpath("/ICECAT-interface/Product/ProductDescription");
    $description_attr   = $description[0]->attributes();
 
    $supplier           = $xml->xpath("/ICECAT-interface/Product/Supplier");
    $supplier_attr      = $supplier[0]->attributes();
 
    $images             = $xml->xpath("/ICECAT-interface/Product/ProductGallery");
 
    $eans               = $xml->xpath("/ICECAT-interface/Product/EANCode");
 
    $featurelogo        = $xml->xpath("/ICECAT-interface/Product/FeatureLogo");
 
    $spec_group         = $xml->xpath("/ICECAT-interface/Product/CategoryFeatureGroup");
    $spec_item          = $xml->xpath("/ICECAT-interface/Product/ProductFeature");
 
    $related            = $xml->xpath("/ICECAT-interface/Product/ProductRelated");
 
    // Set product information
    $p['id']            = (int)$product_attr['ID'];
    $p['name']          = (string)$product_attr['Name'];
    $p['title']         = (string)$product_attr['Title'];
    $p['sku']           = (string)$product_attr['Prod_id'];
    $p['release']       = (string)$product_attr['ReleaseDate'];
    $p['img_thumb']     = (string)$product_attr['ThumbPic'];
    $p['img_small']     = (string)$product_attr['LowPic'];
    $p['img_mid']       = (string)$product_attr['Pic500x500'];
    $p['img_high']      = (string)$product_attr['HighPic'];
    $p['pdf_spec']      = (string)$description_attr['PDFURL'];
    $p['pdf_manual']    = (string)$description_attr['ManualPDFURL'];
    $p['descr_long']    = str_replace('\n','<br />',(string)$description_attr['LongDesc']);
    $p['descr_short']   = (string)$description_attr['ShortDesc'];
    $p['url']           = (string)$description_attr['URL'];
    $p['warrenty']      = (string)$description_attr['WarrantyInfo'];
    $p['category']      = (string)$category[0]->Name[0]['Value'];
    $p['category_id']   = (int)$category[0]['ID'];
 
    // Set brand
    $p['brand_id']      = (int)$supplier_attr['ID'];
    $p['brand_name']    = (string)$supplier_attr['Name'];
 
    // Set images
    foreach($images[0] as $image)
    {
        $image_attr = $image->attributes();
        $p['image'][(int)$image_attr['ProductPicture_ID']]['thumb'] = (string)$image_attr['ThumbPic'];
        $p['image'][(int)$image_attr['ProductPicture_ID']]['small'] = (string)$image_attr['LowPic'];
        $p['image'][(int)$image_attr['ProductPicture_ID']]['mid']   = (string)$image_attr['Pic500x500'];
        $p['image'][(int)$image_attr['ProductPicture_ID']]['high']  = (string)$image_attr['Pic'];
    }
 
    // Set EAN numbers
    foreach($eans as $ean)
    {
        $p['ean'][] = (string)$ean[0]['EAN'];
    }
 
    // Set featurelogos
    foreach($featurelogo as $logo)
    {
        $logo_attr = $logo->attributes();
        $p['featurelogo'][(int)$logo_attr['Feature_ID']]['image'] = (string)$logo_attr['LogoPic'];
        $p['featurelogo'][(int)$logo_attr['Feature_ID']]['descr'] = trim((string)$logo->Descriptions->Description);
    }
 
    // Set specification groups
    foreach($spec_group as $group)
    {
        $p['spec'][(int)$group[0]['ID']]['name'] = (string)$group->FeatureGroup->Name[0]['Value'];
    }
 
    // Set specifications
    foreach($spec_item as $item)
    {
        if($item[0]['Value'] != 'Icecat.biz')
        {
            $p['spec'][(int)$item[0]['CategoryFeatureGroup_ID']]['features'][(int)$item->Feature->Name[0]['ID']]['name']        = (string)$item->Feature->Name[0]['Value'];
            $p['spec'][(int)$item[0]['CategoryFeatureGroup_ID']]['features'][(int)$item->Feature->Name[0]['ID']]['value']       = (string)$item[0]['Value'];
            $p['spec'][(int)$item[0]['CategoryFeatureGroup_ID']]['features'][(int)$item->Feature->Name[0]['ID']]['sign']        = (string)$item->Feature->Measure->Signs->Sign;
            $p['spec'][(int)$item[0]['CategoryFeatureGroup_ID']]['features'][(int)$item->Feature->Name[0]['ID']]['pres_value']  = (string)$item[0]['Presentation_Value'];
        }
    }
 
    // Remove empty specification groups
    foreach($p['spec'] as $key=>$value)
    {
        if(!isset($value['features'])){
            unset($p['spec'][$key]);
        }
    }
 
    // Related products
    foreach($related as $test)
    {
        $p['related'][(int)$test->Product[0]['ID']]['name']     = (string)$test->Product[0]['Name'];
        $p['related'][(int)$test->Product[0]['ID']]['category'] = (int)$test[0]['Category_ID'];
        $p['related'][(int)$test->Product[0]['ID']]['sku']      = (string)$test->Product[0]['Prod_id'];
        $p['related'][(int)$test->Product[0]['ID']]['img']      = (string)$test->Product[0]['ThumbPic'];
        $p['related'][(int)$test->Product[0]['ID']]['brand']    = (string)$test->Product->Supplier[0]['Name'];
        $p['related'][(int)$test->Product[0]['ID']]['brand_id'] = (string)$test->Product->Supplier[0]['ID'];
    }
 
    the_end:
 
    // Return errors if set, else product information
    if(isset($errors)){
        return $errors;
    } else {
        return $p;
    }
}
?>