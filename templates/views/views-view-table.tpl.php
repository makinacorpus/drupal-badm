<table class="table table-striped table-condensed <?php if ($classes) { echo $classes; } ?>"<?php echo $attributes; ?>>
   <?php if (!empty($title) || !empty($caption)) : ?>
     <caption><?php echo $caption . $title; ?></caption>
  <?php endif; ?>
  <?php if (!empty($header)) : ?>
    <thead>
      <tr>
        <?php foreach ($header as $field => $label): ?>
          <th <?php if ($header_classes[$field]) { echo 'class="'. $header_classes[$field] . '" '; } ?>>
            <?php echo $label; ?>
          </th>
        <?php endforeach; ?>
      </tr>
    </thead>
  <?php endif; ?>
  <tbody>
    <?php foreach ($rows as $row_count => $row): ?>
      <tr <?php if ($row_classes[$row_count]) { echo 'class="' . implode(' ', $row_classes[$row_count]) .'"';  } ?>>
        <?php foreach ($row as $field => $content): ?>
          <td <?php if ($field_classes[$field][$row_count]) { echo 'class="'. $field_classes[$field][$row_count] . '" '; } ?><?php echo drupal_attributes($field_attributes[$field][$row_count]); ?>>
            <?php echo $content; ?>
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
