<?php
/**
 * Omeka Analytics Dimensions Plugin: Configuration Form
 *
 * Outputs the configuration form for the config_form hook.
 *
 * @author John Kloor <kloor@bgsu.edu>
 * @copyright 2023 Bowling Green State University Libraries
 * @license MIT
 * @package Analytics Dimensions
 */

$sections = array(
    'HTML' => array(
        array(
            'name' => 'analytics_dimensions_html',
            'label' => __('Tracker'),
            'textarea' => true,
            'explanation' => __(
                'HTML snippet provided by analytics software. Inserted into'.
                ' head element of each public page.'
            )
        ),
        array(
            'name' => 'analytics_dimensions_html_collection',
            'label' => __('Collection'),
            'textarea' => true,
            'explanation' => __(
                'HTML snippet inserted into head element of each public page'.
                ' that is part of a collection. The placeholder %s will be'.
                ' replaced with the collection title encoded as JSON literal.'
            )
        ),
        array(
            'name' => 'analytics_dimensions_html_exhibit',
            'label' => __('Exhibit'),
            'textarea' => true,
            'explanation' => __(
                'HTML snippet inserted into head element of each public page'.
                ' that is part of an exhibit. The placeholder %s will be'.
                ' replaced with the exhibit title encoded as JSON literal.'
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
                <?php elseif (isset($field['textarea'])): ?>
                    <textarea name="<?php echo $field['name']; ?>" id="<?php echo $field['name']; ?>"><?php echo get_option($field['name']); ?></textarea>
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
