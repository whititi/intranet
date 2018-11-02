<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    include 'model.php';
    
    $array = explode("/", $_SERVER['REQUEST_URI']);

    $request = file_get_contents("php://input"); //RECIBIR JSON PARA MODIFICAR/INSERTAR

    foreach ($array as $key => $value) { //VACIAR BLANCOS
        if(empty($value)) {
            unset($array[$key]);
        }
    }

    if(end($array)>0) {
        $id = $array[count($array)];
        $entity = $array[count($array) - 1];
    } else {
        $entity = $array[count($array)];
    }

    $obj = new generic_class;
    $obj->entity = $entity;

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if(isset($id)) {
                $data = $obj->get($id);
            } else {
                $data = $obj->get();
            }
   
            array_pop($data); //ELIMINA LA INFORMACIÓN ADICIONAL

            if(count($data)==0) {
                if(isset($id)) {
                    print_json(404, "Not Found", null);
                } else {
                    print_json(204, "Not Content", null);
                }
            } else {
                print_json(200, "OK", $data);
            }
        break;
        case 'POST':
            if(!isset($id)) {
                $array = json_decode($request, true);
                $obj->data = renderizeData(
                    array_keys($array), 
                    array_values($array)
                );

                $data = $obj->post();
                if($data) {
                    if($obj->conn->lastInsertId() != 0) {
                        $data = $obj->get($obj->conn->lastInsertId());
                        if(count($data)==0) {
                            print_json(201, false, null);
                        } else {
                            array_pop($data);
                            print_json(201, "Created", $data);
                        }
                    } else {
                        print_json(201, false, null);
                    }
                } else {
                    print_json(201, false, null);
                }
            } else {
                print_json(405, "Method Not Allowed", null);
            }
        break;
        case 'PUT':
            if(isset($id)) {
                $info = $obj->get($id);
                array_pop($info);
                if(count($info)!=0) {
                    $array = json_decode($request, true);
                    $obj->data = renderizeData(array_keys($array), array_values($array));
                    $obj->Id = $id;
                    $data = $obj->put();
                    if($data) {
                        $data = $obj->get($id);
                        if(count($data)==0) {
                            print_json(200, false, null);
                        } else {
                            array_pop($data);
                            print_json(200, "OK", $data);
                        }
                    } else {
                        print_json(200, false, null);
                    }
                } else {
                    print_json(404, "Not Found", null);
                }
            } else {
                print_json(405, "Method Not Allowed", null);
            }
        break;
        case 'DELETE':
            if(isset($id)) {
                $info = $obj->get($id);
                if(count($info)==0) {
                    print_json(404, "Not Found", null);
                } else {
                    $obj->Id = $id;
                    $data = $obj->delete();
                    if($data) {
                        array_pop($info);
                        if(count($info)==0) {
                            print_json(404, "Not Found", null);
                        } else {
                            print_json(200, "OK", $info);
                        }
                    } else {
                        print_json(200, false, null);
                    }
                }
            } else {
                print_json(405, "Method Not Allowed", null);
            }
        break;
        default:
            print_json(405, "Method Not Allowed", null);
        break;
    }
    
    function renderizeData($keys, $values) {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                foreach ($keys as $key => $value) {
                    if($key == count($keys) - 1) {
                        $str = $str . $value . ") VALUES (";
                        foreach ($values as $key => $value) {
                            if($key == count($values) - 1) {
                                $str = $str . "'" . $value . "')";
                            } else {
                                $str = $str . "'" . $value . "',";
                            }
                        }
                    } else {
                        if($key == 0) {
                            $str = $str . "(" . $value . ",";
                        } else {
                            $str = $str . $value . ",";
                        }
                    }
                }
                return $str;
            break;
            case 'PUT':
                foreach ($keys as $key => $value) {
                    if($key == count($keys) - 1) {
                        $str = $str . $value . "='" . $values[$key] . "'"; 
                    } else {
                        $str = $str . $value . "='" . $values[$key] . "',"; 
                    }
                }
                return $str;
            break;
        }
    }

    function print_json($status, $mensaje, $data) {
        header("HTTP/1.1 $status $mensaje");
        header("Content-Type: application/json; charset=UTF-8");

        $response['statusCode'] = $status;
        $response['statusMessage'] = $mensaje;
        $response['data'] = $data;

        echo json_encode($response, JSON_PRETTY_PRINT);
    }
?>