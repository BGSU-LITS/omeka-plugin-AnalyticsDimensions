<?php
/**
 * Omeka Analytics Dimensions Plugin
 *
 * @author John Kloor <kloor@bgsu.edu>
 * @copyright 2015 Bowling Green State University Libraries
 * @license MIT
 */

/**
 * Omeka Analytics Dimensions Plugin: Plugin Class
 *
 * @package AnalyticsDimensions
 */
class AnalyticsDimensionsPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Plugin hooks.
     */
    protected $_hooks = array(
        'install',
        'uninstall',
        'config',
        'config_form',
        'public_head'
    );

    /**
     * @var array Plugin options.
     */
    protected $_options = array(
        'analytics_dimensions_trackingId' => '',
        'analytics_dimensions_collection' => 0,
        'analytics_dimensions_exhibit' => 0
    );

    /**
     * Hook to plugin installation.
     */
    public function hookInstall()
    {
        $this->_installOptions();
    }

    /**
     * Hook to plugin uninstallation.
     */
    public function hookUninstall()
    {
        $this->_uninstallOptions();
    }

    /**
     * Hook to plugin configuration form submission.
     *
     * Sets options submitted by the configuration form.
     */
    public function hookConfig($args)
    {
        foreach (array_keys($this->_options) as $option) {
            if (isset($args['post'][$option])) {
                set_option($option, $args['post'][$option]);
            }
        }
    }

    /**
     * Hook to output plugin configuration form.
     *
     * Include form from config_form.php file.
     */
    public function hookConfigForm()
    {
        include 'config_form.php';
    }

    /**
     * Hook to output Google Analytics code in head of public themes.
     */
    public function hookPublicHead()
    {
        // Check that a valid trackign ID was specified.
        $trackingId = get_option('analytics_dimensions_trackingId');

        if (preg_match('/^UA-\d+-\d+$/', $trackingId)) {
            // Output the tracking script and specify tracking ID.
            echo '<script>' . PHP_EOL;
            echo file_get_contents(dirname(__FILE__). '/ga.js') . PHP_EOL;
            echo 'ga("create", "'. $trackingId. '", "auto");' . PHP_EOL;

            // Specify collection title if a dimension and title are available.
            $dimension = (int) get_option('analytics_dimensions_collection');

            if ($dimension) {
                $title = $this->getCollectionTitle();

                if ($title) {
                    echo 'ga("set", "dimension';
                    echo $dimension. '", "'. $title. '")' . PHP_EOL;
                }
            }

            // Specify exhibit title if a dimension and title are available.
            $dimension = (int) get_option('analytics_dimensions_exhibit');

            if ($dimension) {
                $title = $this->getExhibitTitle();

                if ($title) {
                    echo 'ga("set", "dimension';
                    echo $dimension. '", "'. $title. '")' . PHP_EOL;
                }
            }

            // Output to send the page view.
            echo 'ga("send", "pageview");' . PHP_EOL;
            echo '</script>' . PHP_EOL;
        }
    }

    /**
     * Get the current collection's title if available.
     *
     * @return string|null The title of the current collection.
     */
    private function getCollectionTitle()
    {
        // Determine the controller and action for the request.
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        // Other records that may show as part of a collection.
        $other = array('items', 'files', 'exhibits', 'page');

        if ($controller == 'collections' && $action == 'show') {
            // When showing a collection, use the ID.
            $id = $request->getParam('id');
        } elseif (in_array($controller, $other)) {
            // For non-collection controllers, the ID can be a get parameter.
            $id = $request->getParam('collection');

            // Or, the collection ID can come from the files or item shown.
            if (empty($id) && $action == 'show') {
                // Get item ID.
                $item_id = $request->getParam('id');

                if ($controller == 'files') {
                    $file = get_db()->getTable('File')->find($item_id);
                    $item_id = $file->item_id;
                }

                // Get item by that ID.
                $item = get_db()->getTable('Item')->find($item_id);

                if (!empty($item)) {
                    // Get collection ID from the item.
                    $id = $item->collection_id;
                }
            }
        }

        // If an ID is available, try to return title for that collection.
        if (!empty($id)) {
            $collection = get_db()->getTable('Collection')->find($id);

            if ($collection) {
                return metadata($collection, array('Dublin Core', 'Title'));
            }
        }

        // No collection was found.
        return null;
    }

    /**
     * Get the current exhibit's title if available.
     *
     * @return string|null The title of the current exhibit.
     */
    private function getExhibitTitle()
    {
        // Determine the controller for the request.
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controller = $request->getControllerName();

        // Attempt to get the exhibit slug on exhibit or item pages.
        if (in_array($controller, array('exhibits', 'items'))) {
            $slug = $request->getParam('slug');
        }

        // If a slug is available, try to return title for that exhibit.
        if (!empty($slug)) {
            $exhibit = get_db()->getTable('Exhibit')->findBySlug($slug);

            if ($exhibit) {
                return metadata($exhibit, 'title');
            }
        }

        // No exhibit was found.
        return null;
    }
}
