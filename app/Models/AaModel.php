<?php

namespace App\Models;

use Core\Abstracts\Model;

class AaModel extends Model
{

    public function getName($id){
        $this->db->where('id', '=', $id);
        $result = $this->db->get('aa');

        return $result->fetch(\PDO::FETCH_ASSOC)['name']?? '';
    }
}