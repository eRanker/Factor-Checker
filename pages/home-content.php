<main id="main" role="main">

    <?php foreach ($allfactors as $factor) { ?>

        <div class="container">
            <div class="row" class="search-block ">
                <div id="erreport" class="row" class="factor-name" style=" margin-top: 10px;padding: 20px;border: 1px solid red; border-radius: 8px;"> 
                    <div class="row">
                        <div class="col-md-3">
                            <h4>
                                <?php echo $factor->text->friendly_name ?>  
                            </h4>
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


                            $out .= '<div title="' . $impactTitle . '" class="erankertooltip errankerreportficons errankerreportficons-red">';
                            for ($i = 0; $i < $totalIcons; $i++) {
                                if ($i < $impact) {
                                    $out .= '<i class="fa fa-heart"></i>';
                                } else {
                                    $out .= '<i class="fa fa-heart-o"></i>';
                                }
                            }
                            $out .= '</div><!-- .erankertooltip.errankerreportficons.errankerreportficons-red -->';


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

                            $out .= '<div title="' . $dificultTitle . '" class="erankertooltip errankerreportficons errankerreportficons-yellow" >';
                            for ($i = 0; $i < $totalIcons; $i++) {
                                if ($i < $dificulty) {
                                    $out .= '<i class="fa fa-star"></i>'; //fa-star-half-o
                                } else {
                                    $out .= '<i class="fa fa-star-o"></i>';
                                }
                            }

                            $out .= '</div><!-- .erankertooltip.errankerreportficons.errankerreportficons-yellow -->';

                            $out .= '</div><!-- .ericonsrow -->';

                            echo $out;
                            ?>

                        </div>
                        <div class='col-md-5'>
                            <strong style='width: 300px;margin: 0 auto; color: #222;display: block; text-align: center; font: 20px/21px "Permanent Marker", cursive, Georgia, "Times New Roman", Times, serif; -moz-transform: rotate(-2deg); -ms-transform: rotate(-2deg); -o-transform: rotate(-2deg); -webkit-transform: rotate(-2deg);  position: relative;left: 9px;"' class="title">Check how to rank your website better</strong>
                        </div>
                        <div class="col-md-4">
                            <a class="btn btn-default analyze-button" style="text-align: center;font-size: 25px;line-height: 30px;color: #fff;background-image: -webkit-linear-gradient(top, #fa9222 0%, #ef7c01 100%);background-image: -o-linear-gradient(top, #fa9222 0%, #ef7c01 100%);background-image: linear-gradient(to bottom, #fa9222 0%, #ef7c01 100%);background-repeat: repeat-x; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFA9222', endColorstr='#FFEF7C01', GradientType=0);" href="index.php?p=createreport&factor=<?php echo $factor->id ?>"> Create the report</a>
                        </div>
                    </div>
                    <div class="row"><?php echo $factor->text->description_neutral ?>  </div>

                </div>
            </div>                
        </div>
    <?php } ?>

</main>