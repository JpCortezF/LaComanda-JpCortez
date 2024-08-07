<?php
class Mesa
{
    public $id;
    public $codigo;
    public $estado;

    public function NuevaMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigo)
         VALUES (:codigo)");

        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function TraerMesas()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
            $consulta->execute();
            $mesas = $consulta->fetchAll(PDO::FETCH_OBJ);
            return $mesas;
        } catch (Exception $e) {
            throw new Exception("Error al traer las mesas: " . $e->getMessage());
        }
    }

    public static function TraerMesa($id_mesa)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * from mesas WHERE id_mesa = :id_mesa");
            $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_INT);
            $consulta->execute();
            $mesa = $consulta->fetch(PDO::FETCH_ASSOC);
            return $mesa;
        } catch (Exception $e) {
            throw new Exception("Error al traer la mesa: " . $e->getMessage());
        }
    }
    public static function CodigoAlphaNumerico($length = 5)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public static function ActualizarEstadoMesa($id_mesa, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id_mesa = :id_mesa");

        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        return $consulta->execute();
    }
    public static function EliminarMesa($id_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET estado = 'Fuera de servicio' WHERE id_mesa = :id_mesa");
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_INT);
        return $consulta->execute();
    }
    public static function TraerMasUsada()
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT m.*, COUNT(p.id_mesa) as cantidad 
        FROM mesas m
        INNER JOIN pedidos p ON m.id_mesa = p.id_mesa
        GROUP BY p.id_mesa
        ORDER BY cantidad DESC LIMIT 1");
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
    public static function TraerMenosUsada()
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT m.*, COUNT(p.id_mesa) as cantidad 
        FROM mesas m
        INNER JOIN pedidos p ON m.id_mesa = p.id_mesa
        GROUP BY p.id_mesa
        ORDER BY cantidad LIMIT 1");
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function MesaMasFacturo()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT m.id_mesa, m.codigo, SUM(pr.precio * pp.cantidad) AS total_facturado
        FROM mesas m
        INNER JOIN pedidos p ON m.id_mesa = p.id_mesa
        INNER JOIN productopedidos pp ON p.id_pedido = pp.id_pedido
        INNER JOIN productos pr ON pp.id_producto = pr.id_producto
        GROUP BY m.id_mesa
        ORDER BY total_facturado DESC LIMIT 1");
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_OBJ);
    }
    public static function MesaMenosFacturo()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT m.id_mesa, m.codigo, SUM(pr.precio * pp.cantidad) AS total_facturado
        FROM mesas m
        INNER JOIN pedidos p ON m.id_mesa = p.id_mesa
        INNER JOIN productopedidos pp ON p.id_pedido = pp.id_pedido
        INNER JOIN productos pr ON pp.id_producto = pr.id_producto
        GROUP BY m.id_mesa
        ORDER BY total_facturado LIMIT 1");
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_OBJ);
    }
}
