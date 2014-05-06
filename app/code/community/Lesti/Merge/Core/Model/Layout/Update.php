<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 27.02.13
 * Time: 09:35
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Merge_Core_Model_Layout_Update extends Mage_Core_Model_Layout_Update
{
	const HANDLE_ATTRIBUTE = 'data-handle'; //attribute used to store handle

    /**
     * Collect and merge layout updates from file
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param integer|null $storeId
     * @return Mage_Core_Model_Layout_Element
     */
    public function getFileLayoutUpdatesXml($area, $package, $theme, $storeId = null)
    {
        $xml = parent::getFileLayoutUpdatesXml($area, $package, $theme, $storeId);
        if(Mage::getDesign()->getArea() != 'adminhtml') {
            $shouldMergeJs = Mage::getStoreConfigFlag('dev/js/merge_files') &&
                Mage::getStoreConfigFlag('dev/js/merge_js_by_handle');
            $shouldMergeCss = Mage::getStoreConfigFlag('dev/css/merge_css_files') &&
                Mage::getStoreConfigFlag('dev/css/merge_css_by_handle');
            $methods = array();
            if($shouldMergeJs) {
                $methods[] = 'addJs';
            }
            if($shouldMergeCss) {
                $methods[] = 'addCss';
            }
            if($shouldMergeJs || $shouldMergeCss) {
                $methods[] = 'addItem';
            }
            foreach($methods as $method) {
                foreach($xml->children() as $handle => $child){
                    $items = $child->xpath(".//action[@method='".$method."']");
                    foreach($items as $item) {
                        if ($method == 'addItem' && ((!$shouldMergeCss && (string)$item->{'type'} == 'skin_css') || (!$shouldMergeJs && (string)$item->{'type'} == 'skin_js'))){
                            continue;
                        }
                        $params = $item->xpath("params");
                        if(count($params)) {
                            foreach($params as $param){
                                if(trim($param)) {
                                    $param->{0} = (string)$param . ' ' . static::HANDLE_ATTRIBUTE . '="' . $handle . '"';
                                } else {
                                    $param->{0} = static::HANDLE_ATTRIBUTE . '="' . $handle . '"';
                                }
                            }
                        } else {
                            $item->addChild('params', static::HANDLE_ATTRIBUTE . '="'.$handle.'"');
                        }
                    }
                }
            }
        }
        return $xml;
    }

}
