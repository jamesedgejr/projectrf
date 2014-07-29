<?php   

 /* pChart library inclusions */
 include("../../pChart/class/pData.class.php");
 include("../../pChart/class/pDraw.class.php");
 include("../../pChart/class/pPie.class.php");
 include("../../pChart/class/pImage.class.php");
 include('../../main/config.php');
 
 $v = new Valitron\Validator($_GET);
 $v->rule('integer',['critical','high','medium','low','info']);
 if(!$v->validate()) {
    print_r($v->errors());
	exit;
 } 
 
 /* CAT:Pie charts */
 $tmp_failed = $_GET["failed"];
 $failed = ($tmp_failed) ? $tmp_failed: 0.001;
 $tmp_error = $_GET["error"];
 $error = ($tmp_error) ? $tmp_error: 0.001;
 $tmp_passed = $_GET["passed"];
 $passed = ($tmp_passed) ? $tmp_passed: 0.001;

 /* Create and populate the pData object */
 $MyData = new pData();   
 $MyData->addPoints(array("$failed","$error","$passed"),"ScoreA");  
 $MyData->setSerieDescription("ScoreA","Application A");

 /* Define the absissa serie */
 $MyData->addPoints(array("failed","error","passed"),"Labels");
 $MyData->setAbscissa("Labels");

 /* Create the pChart object */
 $myPicture = new pImage(300,250,$MyData,TRUE);

 /* Draw a solid background */
 $Settings = array("R"=>220, "G"=>220, "B"=>220);
 $myPicture->drawFilledRectangle(0,0,300,250,$Settings);

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,299,249,array("R"=>162,"G"=>181,"B"=>205));

 /* Write the picture title */ 
 $myPicture->setFontProperties(array("FontName"=>"../../pChart/fonts/Forgotte.ttf","FontSize"=>11));
 $myPicture->drawText(145,25,"Compliance Level Distribution",array("R"=>75,"G"=>75,"B"=>75,"FontSize"=>14,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE)); 
 
 /* Set the default font properties */ 
 $myPicture->setFontProperties(array("FontName"=>"../../pChart/fonts/Forgotte.ttf","FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));

 /* Create the pPie object */ 
 $PieChart = new pPie($myPicture,$MyData);

 /* Define the slice color */
 $PieChart->setSliceColor(0,array("R"=>255,"G"=>0,"B"=>0));
 $PieChart->setSliceColor(1,array("R"=>255,"G"=>165,"B"=>0));
 $PieChart->setSliceColor(2,array("R"=>255,"G"=>255,"B"=>0));
 $PieChart->setSliceColor(3,array("R"=>0,"G"=>175,"B"=>80));

$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>10));

 /* Draw an AA pie chart */ 
 $PieChart->draw3DPie(150,125,array("Radius"=>125,"WriteValues"=>TRUE,"DataGapAngle"=>5,"DataGapRadius"=>5,"Border"=>TRUE));

 /* Enable shadow computing */ 
 //$myPicture->setShadow(TRUE,array("X"=>3,"Y"=>3,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Write the legend */
 $myPicture->setFontProperties(array("FontName"=>"../../pChart/fonts/Forgotte.ttf","FontSize"=>14));

 /* Write the legend box */ 
 $myPicture->setFontProperties(array("FontName"=>"../../pChart/fonts/Forgotte.ttf","FontSize"=>14,"R"=>75,"G"=>75,"B"=>75));
 $PieChart->drawPieLegend(55,210,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("img.png");
?>
