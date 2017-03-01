<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\widgets\grid;

use yii\grid\CheckboxColumn;

class CoreCheckboxColumn extends CheckboxColumn
{
    protected function renderHeaderCellContent()
    {
        $checkbox = parent::renderHeaderCellContent();
        return $this->wrapTemplate($checkbox);
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        $checkbox = parent::renderDataCellContent($model, $key, $index);

        return $this->wrapTemplate($checkbox);
    }

    protected function wrapTemplate($checkbox){
        return '<div class="checkbox ">
						<label>
							'. $checkbox .'
							<i class="input-helper"></i>
						</label>
					</div>';
    }
}