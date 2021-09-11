<?php

/*
 * CNP validation CLASS
 * Version 1.0.0
 */
class CNPV {

    /**
     type string
     */
    public $error;

    /**
    type string
     */
    public $errorMessage;


    /**
     type string
     */
    protected $cnp;

    /**
     type array
     */
    protected $counties = array(
        1 => "Alba",
        2 => "Arad",
        3 => "Arges",
        4 => "Bacau",
        5 => "Bihor",
        6 => "Bistrita-Nasaud",
        7 => "Botosani",
        8 => "Brasov",
        9 => "Braila",
        10 => "Buzau",
        11 => "Caras-Severin",
        12 => "Cluj",
        13 => "Constanta",
        14 => "Covasna",
        15 => "Dambovita",
        16 => "Dolj",
        17 => "Galati",
        18 => "Gorj",
        19 => "Harghita",
        20 => "Hunedoara",
        21 => "Ialomita",
        22 => "Iasi",
        23 => "Ilfov",
        24 => "Maramures",
        25 => "Mehedinti",
        26 => "Mures",
        27 => "Neamt",
        28 => "Olt",
        29 => "Prahova",
        30 => "Satu Mare",
        31 => "Salaj",
        32 => "Sibiu",
        33 => "Suceava",
        34 => "Teleorman",
        35 => "Timis",
        36 => "Tulcea",
        37 => "Vaslui",
        38 => "Valcea",
        39 => "Vrancea",
        41 => "Bucuresti/Sectorul ?? 1", //fiind 6 ar trebui sectoare
        42 => "Bucuresti/Sectorul ?? 2", // probabil 1 2 3 4 5 6
        43 => "Bucuresti/Sectorul ?? 3",
        44 => "Bucuresti/Sectorul ?? 4",
        45 => "Bucuresti/Sectorul ?? 5",
        46 => "Bucuresti/Sectorul ?? 6",
        51 => "Calarasi",
        52 => "Giurgiu"
    );

    /**
     *type array
     */
    protected $months = array(
        1 => "Ianuarie",
        2 => "Februarie",
        3 => "Martie",
        4 => "Aprilie",
        5 => "Mai",
        6 => "Iunie",
        7 => "Iulie",
        8 => "August",
        9 => "Septembrie",
        10 => "Octombrie",
        11 => "Noiembrie",
        12 => "Decembrie"
    );

    /**
     effective validation class
     */
    public function isCnpValid($cnp) {
       
        $this->cnp = $cnp;
       
        $this->genre = (int)substr($this->cnp, 0, 1);
        $this->month = (int)substr($this->cnp, 3, 2);
        $this->day = (int)substr($this->cnp, 5, 2);
        $this->county = (int)substr($this->cnp, 7, 2);
		
		if( !is_numeric ($this->cnp) ){
			$this->error = 'i1';
			$this->errorMessage = "Formatul CNP este invalid. CNP-ul poate fi numai un format numeric.";
            return FALSE;
		}
		
		// month in 0 or > 12 error
        if( (1 > $this->month || 12 < $this->month) ){
            $this->error = 'i2';
			$this->errorMessage = "Formatul CNP este invalid. Luna nasterii din cpn este invalida.";
            return FALSE;
        }
		
		/*
		max day is 31, 00 not allowed
		*/
		if( (1 > $this->day || 31 < $this->day)  ){
            $this->error = 'i3';
			$this->errorMessage = "Formatul CNP este invalid. Ziua nasterii din cpn este invalida.";
            return FALSE;
        }
		
		/*
		county code max 52
		*/
		if( (!isset($this->counties[$this->county])) ){
            $this->error = 'i4';
			$this->errorMessage = "Formatul CNP este invalid. Codul Judetului este invalid.";
            return FALSE;
        }
		/*
		if all accomplished -> keyValidation
		*/
		if ($this->_keyValidation() === FALSE){
			return FALSE;
		}
    }
   
   
 /**
     returns the year based on the cnp input
     */
    public function getYear() {
        return (
                in_array($this->genre, array(1, 2)) ? 19 : (
                    in_array($this->genre, array(3, 4)) ? 18 : (
                        in_array($this->genre, array(5, 6)) ? 20 : NULL
                    )
                )
            ) .
            substr($this->cnp, 1, 2);
    }
	
    /**
    is the resident of RO 
     */
    public function getResidentInfo() {
        return in_array($this->genre, array(7, 8)) ? 1 : 0;
    }
    /**
    is the holder a stranger 
     */
    public function getStrangerInfo() {
        return in_array($this->genre, array(7, 8, 9)) ? 1 : 0;
    }
   /**
     returns the county based on the cnp input
     */
    public function getCounty() {
        return array (
            $this->county,
            $this->counties[$this->county]
        );
    }
     /**
    gets the gendre M/F
      */
    public function getGenre() {
        return array(
            $this->genre,
            in_array($this->genre, array(1, 3, 5, 7)) ? "m" : (
                in_array($this->genre, array(2, 4, 6, 8)) ? "f" : "n/a"
            )
        );
    }

   

    /**
     returns the month based on the cnp input
     */
    public function getMonth() {
        return array(
            $this->month,
            $this->months[$this->month]
        );
    }

    /**
     retunrn the day of the cnp owmer
     */
    public function getDay() {
        return array(
            $this->day
        );
    }

    /**
     all info of the owner of the CNP
     */
    public function getAllOwnerInfo() {
        return array(
            "genre" => $this->getGenre(),
            "year" => $this->getYear(),
            "month" => $this->getMonth(),
            "day" => $this->getDay(),
            "county" => $this->getCounty(),
            "rezident" => $this->getResidentInfo(),
            "persoana_straina" => $this->getStrangerInfo()
        );
    }

    /**
     check the cnp by key
     */
    private function _keyValidation() {
        // validation key as given
		$key = "279146358279";
        
		// if the string si not 13 char lenght
        if(13 !== strlen($this->cnp)) {
            $this->error = 'v1';
			$this->errorMessage = "Formatul CNP este invalid. CNP-ul nu are 13 cifre in componenta";
            return FALSE;
        }
		
		// the initial sum = 0
        $s = 0;
		
		
        for($i = 0; $i <= 11; $i++)
			{
				// sum is all cnp values * coresponding key index
				$s += $this->cnp[$i] * $key[$i];	
			}
			
       
        $s %= 11;

        if((10 === $s && "1" !== $this->cnp[12]) || (10 > $s && $s != $this->cnp[12])) {
            $this->error = 'v2';
			$this->errorMessage = "Formatul CNP este invalid. Cheia de control nu poate fi validata";
            return FALSE;
        }

        return TRUE;
    }
}


// example and output of the validation 
$a = new CNPV();
$a->isCnpValid("1900619169999"); // type in yours
/*

*/
if ( ! $a->error) {
    /*
     we print out all owenr information
     */
    echo "<pre>";
    print_r($a->getAllOwnerInfo());
    echo "</pre>";

}
else
    echo $a->error; // err code
	echo "<br></br>";
	echo $a->errorMessage; // err msg
