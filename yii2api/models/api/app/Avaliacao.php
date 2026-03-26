<?php
// models/api/app/Avaliacao.php

namespace app\models\api\app;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "avaliacao".
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $loja_id
 * @property int|null $pedido_id
 * @property int|null $produto_id
 * @property int $nota
 * @property string|null $comentario
 * @property string|null $resposta
 * @property string|null $resposta_em
 * @property array|null $fotos
 * @property int $curtidas
 * @property string $status
 * @property string $criado_em
 * @property string $atualizado_em
 * @property string|null $deletado_em
 */
class Avaliacao extends ActiveRecord
{
    const STATUS_PENDENTE = 'pendente';
    const STATUS_APROVADO = 'aprovado';
    const STATUS_REJEITADO = 'rejeitado';

    public static function tableName()
    {
        return '{{%avaliacao}}';
    }

    public function rules()
    {
        return [
            [['usuario_id', 'loja_id', 'nota'], 'required'],
            [['usuario_id', 'loja_id', 'pedido_id', 'produto_id', 'nota', 'curtidas'], 'integer'],
            [['comentario', 'resposta'], 'string'],
            [['fotos', 'status'], 'safe'],
            [['resposta_em', 'criado_em', 'atualizado_em', 'deletado_em'], 'safe'],
            ['nota', 'in', 'range' => range(1, 5)],
            ['status', 'in', 'range' => [self::STATUS_PENDENTE, self::STATUS_APROVADO, self::STATUS_REJEITADO]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_id' => 'Usuário',
            'loja_id' => 'Loja',
            'pedido_id' => 'Pedido',
            'produto_id' => 'Produto',
            'nota' => 'Nota',
            'comentario' => 'Comentário',
            'resposta' => 'Resposta da Loja',
            'resposta_em' => 'Data da Resposta',
            'fotos' => 'Fotos',
            'curtidas' => 'Curtidas',
            'status' => 'Status',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
            'deletado_em' => 'Deletado em',
        ];
    }

    // ===== RELACIONAMENTOS =====
    
    public function getUsuario()
    {
        return $this->hasOne(Usuario::class, ['id' => 'usuario_id']);
    }
    
    public function getLoja()
    {
        return $this->hasOne(\app\models\api\gestor\Loja::class, ['id' => 'loja_id']);
    }
    
    public function getPedido()
    {
        return $this->hasOne(Pedido::class, ['id' => 'pedido_id']);
    }
    
    public function getProduto()
    {
        return $this->hasOne(\app\models\api\gestor\Produto::class, ['id' => 'produto_id']);
    }
    
    // ===== MÉTODOS DE RESPOSTA =====
    
    public function responder($resposta)
    {
        $this->resposta = $resposta;
        $this->resposta_em = date('Y-m-d H:i:s');
        return $this->save(false);
    }
    
    public function aprovar()
    {
        $this->status = self::STATUS_APROVADO;
        return $this->save(false);
    }
    
    public function rejeitar()
    {
        $this->status = self::STATUS_REJEITADO;
        return $this->save(false);
    }
    
    // ===== MÉTODOS DE INTERAÇÃO =====
    
    public function curtir()
    {
        $this->curtidas++;
        return $this->save(false);
    }
    
    public function descurtir()
    {
        if ($this->curtidas > 0) {
            $this->curtidas--;
        }
        return $this->save(false);
    }
    
    // ===== MÉTODOS ESTÁTICOS =====
    
    public static function calcularMediaLoja($lojaId)
    {
        $media = self::find()
            ->where(['loja_id' => $lojaId, 'status' => self::STATUS_APROVADO])
            ->average('nota');
        
        return round($media, 1);
    }
    
    public static function contarAvaliacoes($lojaId)
    {
        return self::find()
            ->where(['loja_id' => $lojaId, 'status' => self::STATUS_APROVADO])
            ->count();
    }
    
    public static function getUltimas($lojaId, $limit = 10)
    {
        return self::find()
            ->where(['loja_id' => $lojaId, 'status' => self::STATUS_APROVADO])
            ->with('usuario')
            ->orderBy(['criado_em' => SORT_DESC])
            ->limit($limit)
            ->all();
    }
}