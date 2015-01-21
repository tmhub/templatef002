<?php

class TM_Templatef002_Upgrade_2_0_0 extends TM_Core_Model_Module_Upgrade
{
    /**
     * Create new products, if they are not exists
     */
    public function up()
    {
        // add new and coming_soon products if no one found
        $todayStartOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $visibility = Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds();
        foreach ($this->getStoreIds() as $storeId) {
            if ($storeId) {
                $store = Mage::app()->getStore($storeId);
            } else {
                $store = Mage::app()->getDefaultStoreView();
            }
            if (!$store) {
                continue;
            }
            $storeId = $store->getId();
            $rootCategory = Mage::getModel('catalog/category')->load($store->getRootCategoryId());

            if (!$rootCategory) {
                continue;
            }
            /**
             * @var Mage_Catalog_Model_Resource_Product_Collection
             */
            $visibleProducts = Mage::getResourceModel('catalog/product_collection');
            $visibleProducts
                ->setStoreId($storeId)
                ->setVisibility($visibility)
                ->addStoreFilter($storeId)
                ->addCategoryFilter($rootCategory)
                ->addAttributeToSort('entity_id', 'desc')
                ->setPageSize(10)
                ->setCurPage(1);

            if (!$visibleProducts->count()) {
                continue;
            }

            foreach ($visibleProducts as $product) {
                $product->load($product->getId());
            }

            // get existing new products
            $newProducts = Mage::getResourceModel('catalog/product_collection')
                ->setStoreId($storeId)
                ->setVisibility($visibility)
                ->addStoreFilter($storeId)
                ->addCategoryFilter($rootCategory)
                ->addAttributeToFilter('news_from_date', array('or'=> array(
                    0 => array('date' => true, 'to' => $todayEndOfDayDate),
                    1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left')
                ->addAttributeToFilter('news_to_date', array('or'=> array(
                    0 => array('date' => true, 'from' => $todayStartOfDayDate),
                    1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left')
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                        array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                        )
                  )
                ->addAttributeToSort('news_from_date', 'desc')
                ->setPageSize(1)
                ->setCurPage(1);

            if (!$newProducts->count()) {
                foreach ($visibleProducts as $product) {
                    $product->setStoreId($storeId);
                    $product->setNewsFromDate($todayStartOfDayDate);
                    $product->save();
                }
            }
        }
    }

    public function getOperations()
    {
        return array(
            'configuration' => $this->_getConfiguration(),
            'cmsblock'      => $this->_getCmsBlocks(),
            'cmspage'       => $this->_getCmsPages()
        );
    }

    private function _getConfiguration()
    {
        return array(
            'design' => array(
                'package/name' => 'f002',
                'theme' => array(
                    'template' => '',
                    'skin'     => '',
                    'layout'   => ''
                )
            ),
            'catalog/product_image/small_width' => 135
        );
    }

    private function _getCmsBlocks()
    {
        return array(
            'showcase' => array(
                'title'      => 'showcase',
                'identifier' => 'showcase',
                'status'     => 1,
                'content'    => <<<HTML
<div id="slider">
    <div class="slidercontrolwr">
        <div class="slidercontrol">
        <a href="#" class="aprev" title="Previous" onclick="my_glider.previous();return false;">Previous</a>
        <a href="#" class="astop" title="Stop" onclick="my_glider.stop();return false">Stop</a>
        <a href="#" class="aplay" title="Play" onclick="my_glider.start();return false">Start</a>
        <a href="#" class="anext" title="Next" onclick="my_glider.next();return false">next</a>
        </div>
    </div>
    <div class="scroller">
        <div class="content">
            <div class="section" id="section1">
                <a href="{{store url=""}}" title=""><img src="{{skin url="images/slider1.jpg"}}" alt="" /></a>
            </div>
            <div class="section" id="section2">
                <a href="{{store url=""}}" title=""><img src="{{skin url="images/slider2.jpg"}}" alt="" /></a>
            </div>
            <div class="section" id="section3">
                <a href="{{store url=""}}" title=""><img src="{{skin url="images/slider3.jpg"}}" alt="" /></a>
            </div>
            <div class="section" id="section4">
                <a href="{{store url=""}}" title=""><img src="{{skin url="images/slider4.jpg"}}" alt="" /></a>
            </div>
        </div>
    </div>
</div>
<script src="{{skin url="js/glider.js"}}" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">
    var my_glider = new Glider('slider', {duration:0.5, autoGlide: false, frequency: 4, initialSection: 'section1'});
</script>
HTML
            ),
            'menu' => array(
                'title' => 'menu',
                'identifier' => 'menu',
                'status' => 1,
                'content' => <<<HTML
<li><a href="{{store url="about"}}"><span>About Us</span></a></li>
<li><a href="{{store url="testimonials"}}"><span>Testimonials</span></a></li>
<li><a href="{{store url="contacts"}}"><span>Contact Us</span></a></li>
HTML
            ),
            'payments' => array(
                'title' => 'payments',
                'identifier' => 'payments',
                'status' => 1,
                'content' => <<<HTML
<img src="{{skin url="images/payments.gif"}}" alt="payments" />
HTML
            )
        );
    }

    private function _getCmsPages()
    {
        return array(
            'home' => array(
                'title'             => 'home',
                'identifier'        => 'home',
                'root_template'     => 'three_columns',
                'meta_keywords'     => '',
                'meta_description'  => '',
                'content_heading'   => '',
                'is_active'         => 1,
                'content'           => <<<HTML
<!-- content -->
HTML
,
                'layout_update_xml' => <<<HTML
<reference name="left">
  <block type="catalog/navigation" before="-" name="catalog.sidebar" template="catalog/navigation/sidebar.phtml"/>
</reference>
<reference name="content">
  <block type="catalog/product_new" name="home.product.new" alias="product_new" template="catalog/product/new.phtml">
    <action method="setProductsCount"><count>6</count></action>
    <action method="addPriceBlockType">
      <type>bundle</type>
      <block>bundle/catalog_product_price</block>
      <template>bundle/catalog/product/price.phtml</template>
    </action>
  </block>
</reference>
<reference name="top_slider">
  <block type="cms/block" name="showcase">
    <action method="setBlockId">
      <block_id>showcase</block_id>
    </action>
  </block>
</reference>
<remove name="right.newsletter"/>
HTML
            )
        );
    }
}
