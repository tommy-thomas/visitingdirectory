<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 11/20/17
 * Time: 3:35 PM
 */

namespace UChicago\AdvisoryCouncil;


use GuzzleHttp\Promise\Promise;

class CommitteeMemberFactory
{

    private $json_payload;
    private $member;

    public function member($json_payload, $chair = false)
    {
        if (is_null($json_payload)) {
            return false;
        }
        $this->member = new CommitteeMember();
        $this->json_payload = $json_payload;
        $this
            ->info()
            ->chair($chair)
            ->addresses()
            ->degrees()
            ->employment()
            ->email()
            ->phone();
        return $this->member;
    }

    public static function isActive(\stdClass $member = null)
    {
        if (!is_null($member) && !is_null($member->ID_NUMBER)) {
            return $member->TMS_COMMITTEE_STATUS_CODE == "Active" ? true : false;
        }
        return false;
    }

    public function chairsArray($members = [])
    {
        $chairs = array();
        foreach ($members as $key => $member) {
            if (self::isActive($member) && isset($member->COMMITTEE_ROLE_CODE) && $member->COMMITTEE_ROLE_CODE == "CH") {
                $chairs[$member->COMMITTEE_CODE] = $member->ID_NUMBER;
            }
        }
        return $chairs;
    }

    public function idNumbersAsQueryString($members = [])
    {
        $id_numbers = array_map(function ($ar) {
            return self::isActive($ar) ? $ar->ID_NUMBER : null;
        }, $members);
        $id_numbers = array_filter($id_numbers, function ($value) {
            if (!is_null($value) && !empty($value)) {
                return $value;
            }
        });
        return $this->idsAsQueryString($id_numbers);
    }


    private function idsAsQueryString($id_numbers = [])
    {
        $ids_as_array_string = "";
        foreach ($id_numbers as $id) {
            $ids_as_array_string .= "keys[]=" . $id . "&";
        }
        return substr($ids_as_array_string, 0, (strlen($ids_as_array_string) - 1));
    }

    private function chair($chair = false)
    {
        $this->member->setChair($chair);
        return $this;
    }

    private function info()
    {
        if (!isset($this->json_payload->info)) {
            return $this;
        }
        $info = $this->json_payload->info;
        //  print $info->RECORD_TYPE_CODE . "\n";
        if ($info->TMS_RECORD_TYPE_CODE == "CH" ||
            $info->RECORD_TYPE_CODE == "CH" ||
            $info->PERSON_OR_ORG == "CH") {
            print "Committee Chair\n";
        }

        $this->member->setName($info->FIRST_NAME, $info->MIDDLE_NAME, $info->LAST_NAME);
        $this->member->setIDNumber($info->ID_NUMBER);
        return $this;
    }

    private function addresses()
    {
        if (!isset($this->json_payload->addresses)) {
            return $this;
        }
        $addresses_data = $this->addressesFilter($this->json_payload->addresses);
        $this->member->setAddress($addresses_data[0]->STREET, $addresses_data[0]->CITY, $addresses_data[0]->STATE_CODE, $addresses_data[0]->ZIPCODE, $addresses_data[0]->FOREIGN_CITYZIP, $addresses_data->COUNTRY_CODE);
        return $this;
    }


    private function addressesFilter($addresses)
    {
        foreach ($addresses as $key => $address) {
            if (isset($address->ADDR_PREF_IND) && $address->ADDR_PREF_IND != "Y") {
                unset($addresses[$key]);
            }
        }
        return array_values($addresses);
    }

    private function degrees()
    {
        if (!isset($this->json_payload->degree)) {
            return $this;
        }
        $this->member->setDegrees($this->degreesFilter($this->json_payload->degree));
        return $this;
    }

    private function degreesFilter($degrees)
    {
        foreach ($degrees as $key => $degree) {
            if (isset($degree->LOCAL_IND) && $degree->LOCAL_IND != "Y") {
                unset($degrees[$key]);
            }
        }
        return $degrees;
    }

    private function employment()
    {
        if (!isset($this->json_payload->employment)) {
            return $this;
        }
        $employment_data = array_values($this->employmentFilter($this->json_payload->employment));
        $this->member->setEmploymentData($employment_data[0]->JOB_TITLE, $employment_data[0]->EMPLOYER_NAME, $employment_data[0]->ORG_NAME);
        return $this;
    }

    private function employmentFilter($employments)
    {
        foreach ($employments as $key => $employment) {
            if (isset($employment->PRIMARY_EMP_IND) && $employment->PRIMARY_EMP_IND != "Y") {
                unset($employments[$key]);
            }
        }
        return $employments;
    }

    private function email()
    {
        if (!isset($this->json_payload->email)) {
            return $this;
        }
        $email = $this->emailFilter($this->json_payload->email)[0]->EMAIL_ADDRESS;
        $this->member->setEmail($email);
        return $this;
    }

    private function emailFilter($emails)
    {
        foreach ($emails as $key => $email) {
            if (isset($email->PREFERRED_IND) && isset($email->EMAIL_TYPE_CODE)
                && ($email->PREFERRED_IND != "Y" || $email->EMAIL_TYPE_CODE != "H")) {
                unset($emails[$key]);
            }
        }
        return $emails;
    }

    private function phone()
    {
        if (!isset($this->json_payload->telephone)) {
            return false;
        }
        $telephone_data = array_values($this->phoneFilter($this->json_payload->telephone));
        if (isset($telephone_data[0]->TELEPHONE_NUMBER) && strlen($telephone_data[0]->TELEPHONE_NUMBER) == 7) {
            $this->member->setPhone($telephone_data[0]->AREA_CODE
                . "-" . substr($telephone_data[0]->TELEPHONE_NUMBER, 0, 3)
                . "-" . substr($telephone_data[0]->TELEPHONE_NUMBER, 3, 4));
        }
        return $this;
    }

    private function phoneFilter($telephone)
    {
        foreach ($telephone as $key => $phone) {
            if (isset($phone->PREFERRED_IND) && $phone->PREFERRED_IND != "Y") {
                unset($telephone[$key]);
            }
        }
        return $telephone;
    }

    private function compare(CommitteeMember $a, CommitteeMember $b)
    {
        return strcmp($a->sort_token(), $b->sort_token());
    }

    public function sortData($data = array())
    {
        usort($data, array($this, "compare"));
        return $data;
    }
}