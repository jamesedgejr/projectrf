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
 $tmp_info = $_GET["info"];
 
 /* Create and populate the pData object */
 $MyData = new pData();   
 $MyData->addPoints(array("$failed","$error","$passed","$info"),"ScoreA");  
 $MyData->setSerieDescription("ScoreA","Application A");

 /* Define the absissa serie */
 $MyData->addPoints(array("Failed: $tmp_failed","Error:  $tmp_error","Passed:  $tmp_passed"),"Labels");
 $MyData->setAbscissa("Labels");

 /* Create the pChart object */
 $myPicture = new pImage(430,200,$MyData,TRUE);

 /* Draw a solid background */
 $Settings = array("R"=>220, "G"=>220, "B"=>220);
 $myPicture->drawFilledRectangle(0,0,430,200,$Settings);

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,429,199,array("R"=>162,"G"=>181,"B"=>205));

 /* Write the picture title */ 
 $myPicture->setFontProperties(array("FontName"=>"../../pChart/fonts/Silkscreen.ttf","FontSize"=>6));
 $myPicture->drawText(125,25,"$title",array("R"=>75,"G"=>75,"B"=>75));

 /* Set the default font properties */ 
 $myPicture->setFontProperties(array("FontName"=>"../../pChart/fonts/Forgotte.ttf","FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));

 /* Create the pPie object */ 
 $PieChart = new pPie($myPicture,$MyData);

 /* Define the slice color */
 $PieChart->setSliceColor(0,array("R"=>255,"G"=>0,"B"=>0));
 $PieChart->setSliceColor(1,array("R"=>255,"G"=>165,"B"=>0));
 $PieChart->setSliceColor(2,array("R"=>255,"G"=>255,"B"=>0));
 $PieChart->setSliceColor(3,array("R"=>0,"G"=>175,"B"=>80));

 /* Draw an AA pie chart */ 
 $PieChart->draw3DPie(210,100,array("WriteValues"=>TRUE,"DataGapAngle"=>10,"DataGapRadius"=>6,"Border"=>TRUE));

 /* Enable shadow computing */ 
 $myPicture->setShadow(TRUE,array("X"=>3,"Y"=>3,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Write the legend */
 $myPicture->setFontProperties(array("FontName"=>"../../pChart/fonts/pf_arma_five.ttf","FontSize"=>6));
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));

 /* Write the legend box */ 
 $myPicture->setFontProperties(array("FontName"=>"../../pChart/fonts/Silkscreen.ttf","FontSize"=>6,"R"=>75,"G"=>75,"B"=>75));
 $PieChart->drawPieLegend(85,165,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("img.png");
?>
