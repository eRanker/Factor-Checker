<?php
global $allfactors;


$arrayCategory = array();

foreach ($allfactors as $value) {
    $arrayCategory[$value->category_icon] = $value->category_friendly_name;
}

$arrayCategory = array_filter(array_unique($arrayCategory));
?>
<div class="container" id="erreporthomepage">
    <?php foreach ($arrayCategory as $key => $value)        
     { ?>
        <div class="group">
            <div class="row">
                <div class="col-md-12 advertise leaderboard">
                   <img src="<?php echo $key ?>" class="ercategoryicon" alt="{icon}" /> <?php echo $value ?>
                </div>
            </div>
            <hr class="graded mobile-hide">

            <div class="row">
                <?php
                $countDivider = 0;
                foreach ($allfactors as $factor) {
                    if (strcasecmp($value, $factor->category_friendly_name) === 0) {
                        $countDivider++;
                        ?>

                        <div onclick="window.location='index.php?p=createreport&factor=<?php echo $factor->id ?>';
                                return false;" class="col-md-4 intro-feature tool-showcase">

                            <h3 class="smaller bold"><?php echo $factor->text->friendly_name ?> </h3>
                            <span class="icon price-tag2" aria-hidden="true">
                                <?php
                                $out = '<div class="ericonsrow">';

                                $totalIcons = 3;


                                $impactTitle = "High Impact";
                                $impact = 3;
                                if ($factor->correlation < 0.1) {
                                    $impactTitle = "Low Impact";
                                    $impact = 1;
                                } else {
                                    if ($factor->correlation < 0.25) {
                                        $impactTitle = "Medium Impact";
                                        $impact = 2;
                                    }
                                }


                                $out .= '<span title="' . $impactTitle . '" class="erankertooltip errankerreportficons errankerreportficons-red">';
                                for ($i = 0; $i < $totalIcons; $i++) {
                                    if ($i < $impact) {
                                        $out .= '<i class="fa fa-heart"></i>';
                                    } else {
                                        $out .= '<i class="fa fa-heart-o"></i>';
                                    }
                                }
                                $out .= '</span><!-- .erankertooltip.errankerreportficons.errankerreportficons-red -->';


                                $dificulty = 1;
                                $dificultTitle = "Easy to Solve";
                                if (strcasecmp($factor->difficulty_level, "MEDIUM") === 0) {
                                    $dificultTitle = "Moderate difficulty";
                                    $dificulty = 2;
                                }
                                if (strcasecmp($factor->difficulty_level, "HARD") === 0) {
                                    $dificultTitle = "Hard to Solve";
                                    $dificulty = 3;
                                }

                                $out .= '<span title="' . $dificultTitle . '" class="erankertooltip errankerreportficons errankerreportficons-yellow" >';
                                for ($i = 0; $i < $totalIcons; $i++) {
                                    if ($i < $dificulty) {
                                        $out .= '<i class="fa fa-star"></i>'; //fa-star-half-o
                                    } else {
                                        $out .= '<i class="fa fa-star-o"></i>';
                                    }
                                }

                                $out .= '</span><!-- .erankertooltip.errankerreportficons.errankerreportficons-yellow -->';

                                $out .= '</div><!-- .ericonsrow -->';

                                echo $out;
                                ?>
                            </span>
                            <?php echo stripslashes(html_entity_decode((isset($factor->text->description_neutral)) ? $factor->text->description_neutral  :'')) ?>
                        </div>
            <?php if ($countDivider == 3) { ?>
                            <hr class="clearfix graded mobile-hide">

                            <?php
                            $countDivider = 0;
                        }
                        ?>
                        <?php
                    }
                }
                ?>
                <hr class="clearfix graded mobile-hide" style="margin-bottom:20px">
            </div>
        </div>
<?php } ?>
</div>