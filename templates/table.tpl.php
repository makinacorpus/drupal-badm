<table<?php echo drupal_attributes($attributes); ?>>

  <?php if ($caption): ?>
    <caption>
      <?php echo $caption; ?>
    </caption>
  <?php endif; ?>

  <?php if ($colgroups): ?>
    <?php foreach ($colgroups as $colgroup): ?>

      <?php
        // Ugly code from Drupal, can't really get rid of this one.
        $attributes = [];
        // Check if we're dealing with a simple or complex column
        if (isset($colgroup['data'])):
          foreach ($colgroup as $key => $value):
            if ($key == 'data'):
              $cols = $value;
            else:
              $attributes[$key] = $value;
            endif;
          endforeach;
        else:
          $cols = $colgroup;
        endif;
      ?>

      <?php if (is_array($cols) && count($cols)): ?>
        <colgroup<?php echo drupal_attributes($attributes); ?>>
          <?php foreach ($cols as $col): ?>
            <col<?php echo drupal_attributes($col); ?>/>
          <?php endforeach; ?>
        </colgroup>
      <?php else: ?>
        <colgroup<?php echo drupal_attributes($attributes)?>/>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if (count($header)): ?>
    <thead>
      <tr>
        <?php foreach ($header as $cell): ?>

          <?php
            // @see _theme_table_cell().
            // Ugly code from Drupal, can't really get rid of this one.
            $cell = tablesort_header($cell, $header, $sortheader);
            if (is_array($cell)):
              $data = isset($cell['data']) ? $cell['data'] : '';
              unset($cell['data'], $cell['header']);
            else:
              $data = $cell;
              $cell = [];
            endif;
          ?>

          <th<?php echo drupal_attributes($cell); ?>>
            <?php echo render($data); ?>
          </th>
        <?php endforeach; ?>
      </tr>
    </thead>
  <?php else: ?>
    <?php $ts = []; ?>
  <?php endif; ?>

  <?php if (count($rows)): ?>
    <tbody>
      <?php foreach ($rows as $row): ?>

        <?php
          // Ugly code from Drupal, can't really get rid of this one.
          $attributes = [];
          // Check if we're dealing with a simple or complex row
          if (isset($row['data'])):
            foreach ($row as $key => $value):
              if ($key == 'data'):
                $cells = $value;
              else:
                $attributes[$key] = $value;
              endif;
            endforeach;
          else:
            $cells = $row;
          endif;
        ?>

        <?php if (count($cells)): ?>
          <tr<?php echo drupal_attributes($attributes); ?>>
            <?php foreach (array_values($cells) as $i => $cell): ?>

              <?php
                $cell = tablesort_cell($cell, $header, $sortheader, $i);

                // @see _theme_table_cell().
                // Ugly code from Drupal, can't really get rid of this one.
                if (is_array($cell)):
                  $data = isset($cell['data']) ? $cell['data'] : '';
                  unset($cell['data'], $cell['header']);
                else:
                  $data = $cell;
                  $cell = [];
                endif;
              ?>

              <td<?php echo drupal_attributes($cell); ?>>
                <?php echo render($data); ?>
              </td>
            <?php endforeach; ?>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  <?php endif; ?>
</table>