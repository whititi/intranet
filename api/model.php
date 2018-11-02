<?php
    include 'core/db.php';
    include 'core/api.php';

    class generic_class extends db_model implements api {
        public $entity;
        public $data;
  
        function get($id = 0) {
            if($id == 0) {
                return $this->get_query(
                    sprintf(
                        "SELECT * FROM %s", 
                        $this->entity
                    )
                );
            } else {
                return $this->get_query(
                    sprintf(
                        "SELECT * FROM %s WHERE id = %d", 
                        $this->entity, 
                        $id
                    )
                );
            }
        }

        function post() {
            return $this->set_query(
                sprintf(
                    "INSERT INTO %s %s",
                    $this->entity,
                    $this->data
                )
            );
        }

        function put() {
            return $this->set_query(
                sprintf(
                    "UPDATE %s SET %s WHERE id = %d", 
                    $this->entity,
                    $this->data, 
                    $this->id
                )
            );
        }

        function delete() {
            return $this->set_query(
                sprintf(
                    "DELETE FROM %s WHERE id = %d",
                    $this->entity,
                    $this->id
                )
            );
        }
    }
?>