<?php

use yii\web\View;

/* @var $this View */
/* @var $attributeGroups array */
/* @var $matrixHeaders array */
/* @var $matrixRows array */
/* @var $noImpactRows array */
/* @var $independentRows array */
/* @var $incremental array */
/* @var $view int */

?>

<?php if ($view == 1) { ?>
    <?= $this->render('price-matrix/basic/matrix', compact('matrixHeaders', 'matrixRows', 'incremental')) ?>
    <?= $this->render('price-matrix/independent', compact('independentRows')) ?>
    <?= $this->render('price-matrix/no-impact', compact('noImpactRows')) ?>
<?php } else if ($view == 2) { ?>
    <?= $this->render('price-matrix/legacy/matrix', compact('attributeGroups', 'incremental')) ?>
    <?= $this->render('price-matrix/independent', compact('independentRows')) ?>
    <?= $this->render('price-matrix/no-impact', compact('noImpactRows')) ?>
<?php } else if ($view == 3) { ?>
    <?= $this->render('price-matrix/dropdown/matrix', compact('matrixHeaders', 'matrixRows', 'attributeGroups', 'incremental')) ?>
    <?= $this->render('price-matrix/independent', compact('independentRows')) ?>
    <?= $this->render('price-matrix/no-impact', compact('noImpactRows')) ?>
<?php } ?>