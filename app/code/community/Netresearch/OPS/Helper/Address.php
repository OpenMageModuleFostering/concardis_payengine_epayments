<?php
/**
 * Netresearch_OPS_Helper_Address
 *
 * @package
 * @copyright 2016 Netresearch
 * @author    Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license   OSL 3.0
 */
class Netresearch_OPS_Helper_Address extends Mage_Core_Helper_Abstract
{

    const OPTION_A_ADDITION_1   = 'A_Addition_to_address_1';
    const OPTION_A_STREET_NAME  = 'A_Street_name_1';
    const OPTION_A_HOUSE_NUMBER = 'A_House_number_1';
    const OPTION_A_ADDITION_2   = 'A_Addition_to_address_2';
    const OPTION_B_ADDITION_1   = 'B_Addition_to_address_1';
    const OPTION_B_STREET_NAME  = 'B_Street_name';
    const OPTION_B_HOUSE_NUMBER = 'B_House_number';
    const OPTION_B_ADDITION_2   = 'B_Addition_to_address_2';


    /**
     * split street into street name, number and additional street information
     *
     * @param string $street
     *
     * @return array
     */
    public function splitStreet($street)
    {
        $result = array(
            'street_name'   => $street,
            'street_number' => '',
            'supplement'    => ''
        );

        if (preg_match($this->getStreetSplitter(), $street, $matches)) {

            // Pattern A
            if (isset($matches[self::OPTION_A_STREET_NAME]) && !empty($matches[self::OPTION_A_STREET_NAME])) {

                $result['street_name'] = trim($matches[self::OPTION_A_STREET_NAME]);

                if (isset($matches[self::OPTION_A_HOUSE_NUMBER]) && !empty($matches[self::OPTION_A_HOUSE_NUMBER])) {
                    $result['street_number'] = trim($matches[self::OPTION_A_HOUSE_NUMBER]);
                }

                if (isset($matches[self::OPTION_A_ADDITION_1]) && isset($matches[self::OPTION_A_ADDITION_2])) {
                    $result['supplement'] = trim($matches[self::OPTION_A_ADDITION_1] . ' '
                        . $matches[self::OPTION_A_ADDITION_2]
                    );
                }

                return $result ;

            // Pattern B
            } elseif (isset($matches[self::OPTION_B_STREET_NAME]) && !empty($matches[self::OPTION_B_STREET_NAME])) {

                $result['street_name'] = trim($matches[self::OPTION_B_STREET_NAME]);

                if (isset($matches[self::OPTION_B_HOUSE_NUMBER]) && !empty($matches[self::OPTION_B_HOUSE_NUMBER])) {
                    $result['street_number'] = trim($matches[self::OPTION_B_HOUSE_NUMBER]);
                }

                if (isset($matches[self::OPTION_B_ADDITION_1]) && isset($matches[self::OPTION_B_ADDITION_2])) {
                    $result['supplement'] = trim($matches[self::OPTION_B_ADDITION_1] . ' '
                        . $matches[self::OPTION_B_ADDITION_2]
                    );
                }


                return $result;
            }
        }

        return $result;
    }

    /**
     * Regex to analyze addresses and split them into the groups Street Name, House Number and Additional Information
     * Pattern A is addition number street addition
     * Pattern B is addition street number addition
     *
     * @return string
     */
    protected function getStreetSplitter()
    {
        return "/\\A\\s*
 (?:
   #########################################################################
   # Option A: [<Addition to address 1>] <House number> <Street name>      #
   # [<Addition to address 2>]                                             #
   #########################################################################
   (?:
     (?P<A_Addition_to_address_1>.*?)
     ,\\s*
   )?
   # Addition to address 1
   (?:No\\.\\s*)?
   (?P<A_House_number_1>
     \\pN+[a-zA-Z]?
     (?:\\s*[-\\/\\pP]\\s*\\pN+[a-zA-Z]?)*
   )
   # House number
   \\s*,?\\s*
   (?P<A_Street_name_1>
     (?:[a-zA-Z]\\s*|\\pN\\pL{2,}\\s\\pL)
     \\S[^,#]*?
     (?<!\\s)
   )
   # Street name
   \\s*
   (?:
     (?:
       [,\\/]|
       (?=\\#)
     )
     \\s*
     (?!\\s*No\\.)
     (?P<A_Addition_to_address_2>
       (?!\\s)
       .*?
     )
   )?
   # Addition to address 2
   |
   #########################################################################
   # Option B: [<Addition to address 1>] <Street name> <House number>      #
   # [<Addition to address 2>]                                             #
   #########################################################################
   (?:
     (?P<B_Addition_to_address_1>.*?)
     ,\\s*
     (?=.*[,\\/])
   )?
   # Addition to address 1
   (?!\\s*No\\.)
   (?P<B_Street_name>
     \\S\\s*\\S
     (?:
       [^,#]
       (?!\\b\\pN+\\s)
     )*?
     (?<!\\s)
   )
   # Street name
   \\s*[\\/,]?\\s*
   (?:\\sNo\\.)?
   \\s+
   (?P<B_House_number>
     \\pN+\\s*-?[a-zA-Z]?
     (?:
       \\s*[-\\/\\pP]?\\s*\\pN+
       (?:\\s*[\\-a-zA-Z])?
     )*|
     [IVXLCDM]+
     (?!.*\\b\\pN+\\b)
   )
   (?<!\\s)
   # House number
   \\s*
   (?:
     (?:
       [,\\/]|
       (?=\\#)|
       \\s
     )
     \\s*
     (?!\\s*No\\.)
     \\s*
     (?P<B_Addition_to_address_2>
       (?!\\s)
       .*?
     )
   )?
   # Addition to address 2
 )
 \\s*\\Z/x";
    }
}
