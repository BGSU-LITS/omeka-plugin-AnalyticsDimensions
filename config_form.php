<?php
/**
 * Omeka Analytics Dimensions Plugin: Configuration Form
 *
 * Outputs the configuration form for the config_form hook.
 *
 * @author John Kloor <kloor@bgsu.edu>
 * @copyright 2015 Bowling Green State University Libraries
 * @license MIT
 * @package Analytics Dimensions
 */

$select = range(0, 20);
$select[0] = 'None';

$sections = array(
    'Google Analytics' => array(
        array(
            'name' => 'analytics_dimensions_trackingId',
            'label' => __('Tracking ID'),
            'explanation' => __('Example: UA-000000-01')
        ),
        array(
            'name' => 'analytics_dimensions_collection',
            'label' => __('Collection Dimension'),
            'select' => $select,
            'explanation' => __(
                'Create a Custom Dimension in Google Analytics for collection'.
                ' names, and provide the index for that dimension.'
            )
        ),
        array(
            'name' => 'analytics_dimensions_exhibit',
            'label' => __('Exhibit Dimension'),
            'select' => $select,
            'explanation' => __(
                'Create a Custom Dimension in Google Analytics for exhibit'.
                ' names, and provide the index for that dimension.'
            )
        )
    )
);
?>

<?php foreach ($sections as $section => $fields): ?>
    <h2><?php echo $section; ?></h2>

    <?php foreach ($fields as $field): ?>
        <div class="field">
            <div class="two columns alpha">
                <label for="<?php echo $field['name']; ?>">
                    <?php echo $field['label']; ?>
                </label>
            </div>
            <div class="inputs five columns omega">
                <?php if (isset($field['select'])): ?>
                    <select name="<?php echo $field['name']; ?>" id="<?php echo $field['name']; ?>">
                        <?php foreach ($field['select'] as $value => $option): ?>
                            <option value="<?php echo $value; ?>"<?php if (get_option($field['name']) == $value) echo ' selected'; ?>>
                                <?php echo $option; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif (isset($field['checkbox'])): ?>
                    <input type="hidden" name="<?php echo $field['name']; ?>" value="">
                    <input type="checkbox" name="<?php echo $field['name']; ?>" id="<?php echo $field['name']; ?>" value="<?php echo $field['checkbox']; ?>"<?php if (get_option($field['name']) == $field['checkbox']) echo ' checked'; ?>>
                <?php else: ?>
                    <input type="<?php print(empty($field['password']) ? 'text' : 'password'); ?>" name="<?php echo $field['name']; ?>" id="<?php echo $field['name']; ?>" value="<?php echo get_option($field['name']); ?>">
                <?php endif; ?>

                <?php if (isset($field['explanation'])): ?>
                    <p class="explanation">
                        <?php echo $field['explanation']; ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>
