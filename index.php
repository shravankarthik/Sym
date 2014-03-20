<?php
class Disease
{
public $name="";
public $mainsymptom="";
public $othersymptom="";
 public function __construct($nam,$mainsym,$othersym)
	{
		$this->name = $nam;
		$this->mainsymptom = $mainsym;
		$this->othersymptom = $othersym;
	}
 public function display()
	{
		echo $this->name.' '.$this->mainsymptom.' '.$this->othersymptom.'<br>';
	}
}
$diseases = array();
$counter =0;
$xml = simplexml_load_file('symptom.xml');
if(isset($_POST['symptoms']) && !(empty($_POST['symptoms'])))
{
  $symplist = $_POST['symptoms'];
  echo "<br> previous $symplist <br>";
  #split based on , i.e if various symptoms are entered 	
  $list = split(",",$symplist);
  foreach($xml->disease as $disease)
  {
	# for each disease read from the xml file assign it to an array and initialise with values name , main-symptom and other-symptoms
    $diseases[$counter] = new Disease($disease->name,$disease->mainsym,$disease->othersym);
    $counter++;
  }

  $size = $counter;
  $matches = array(); # is an array that stores how many symptoms of each disease matches with the inputed symptom
  $matchesmain = array(); # array which stores boolean value i.e if the main symptom is a match with the inputed symptom the value for 
			  #that disease is set to 1 else is 0
  $counter =0;
  while($counter<$size)
  {
    $matches[$counter]=0;
    $matchesmain[$counter]=0;
    $listofsym = split(",",$diseases[$counter]->othersymptom);
    foreach($list as $individual)
    {
      if($individual == $diseases[$counter]->mainsymptom)
	{
	$matches[$counter]=$matches[$counter]+1;
	#the symptoms entered by user matches the main symptom of a disease
	$matchesmain[$counter] = 1;
	continue;
	}
      else
	{
	 foreach($listofsym as $listofsymread)
		{
		 if($individual == $listofsymread)
			{
			 $matches[$counter] = $matches[$counter] + 1;
			 break;
			}
		}
	}	
    }
    $counter++;
  }
  $counter =0;
  while($counter>$size)
  {
    if($matchesmain[$counter]==1)
    break;
    $counter++;
  }
  if($counter==$size)
  {
    $finaldisease = $diseases[0]->name;
    $maxs = $matches[0];
  }
  else
  {
    $finaldisease = $diseases[$counter]->name;
    $maxs = $matches[$counter];
  }
  $counter=0;
  $counter2=0;
  $finaldiseaseno=array();
  while($counter<$size)
  {
  if($matches[$counter]>$maxs)
  {
    $finaldisease = $diseases[$counter]->name;
    $maxs = $matches[$counter];
  }
else if($matches[$counter]==$maxs)
      {
        if($matchesmain[$counter]==1)
	{
	  $temp = $diseases[$counter]->name;
	  $finaldiseaseno[$counter2]=$temp;
	  $counter2++;
        }
      }
    $counter++;
  }
  $counter2= sizeof($finaldiseaseno);
  if($counter2>1)
  {
    # here we can ask more questions to narrow down which disease exactly the patient suffers from as the inputed symptoms list isnt sufficient
    # to narrow down to 1 disease
    $counter =0;
    while($counter<$counter2)
    {
     echo "$finaldiseaseno[$counter] <br>";
     $counter++;
    }
  }
  else if($counter2==1)
  {
   #disease the patient suffers from
   echo "<br><br>$finaldisease<br>";
  }
  else 
  {
   # if the input is not on the list of knowns symptoms for any disease
   echo "<br>No disease found for this symptom<br>";
  }
}
?>
<form action = "index.php" method = "POST">
<br>Symptom <br> <input type = "text" name = "symptoms">
<br>
<input type = "submit" value = "find my illness">
</form>
