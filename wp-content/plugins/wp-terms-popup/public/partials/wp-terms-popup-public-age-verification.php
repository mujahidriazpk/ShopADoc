<?php
/**
 * Provide a dropdown view for the plugin.
 *
 * @link       https://linksoftwarellc.com
 * @since      1.0.0
 *
 * @package    Wp_Terms_Popup_Age
 * @subpackage Wp_Terms_Popup_Age/public/partials
 */
?>
<table class="wptpa-dropdowns">
    <tr>
        <?php foreach ($columns as $df => $column) : ?>
        <td>
            <?php echo $column['label']; ?><br>
            <select id="wptpa-<?php echo $df; ?>" class="wptpa-dropdown" data-target="<?php echo $column['target']; ?>">
                <option value=""></option>
                <?php foreach ($column['options'] as $option) : ?>
                <option value="<?php echo $option['value']; ?>"><?php echo $option['label']; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <?php endforeach; ?>
    </tr>
</table>