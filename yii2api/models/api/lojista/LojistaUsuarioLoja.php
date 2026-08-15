<?php

namespace app\models\api\lojista;

use Yii;
use yii\db\ActiveRecord;
use app\models\api\app\Loja;

/**
 * This is the model class for table "store_usuario_loja".
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $loja_id
 * @property string $funcao
 * @property int $status
 * @property array|null $permissoes
 * @property string|null $ultimo_acesso_em
 * @property string $criado_em
 * @property string $atualizado_em
 *
 * @property LojistaUsuario $usuario
 * @property Loja $loja
 */
class LojistaUsuarioLoja extends ActiveRecord
{
    const STATUS_ATIVO = 1;
    const STATUS_INATIVO = 0;

    const FUNCAO_PROPRIETARIO = 'proprietario';
    const FUNCAO_GERENTE = 'gerente';
    const FUNCAO_VENDEDOR = 'vendedor';

    public static function tableName()
    {
        return '{{%store_usuario_loja}}';
    }

    public function rules()
    {
        return [
            [['usuario_id', 'loja_id', 'funcao'], 'required'],
            [['usuario_id', 'loja_id', 'status'], 'integer'],
            [['permissoes'], 'safe'],
            [['funcao'], 'string', 'max' => 20],
            ['funcao', 'in', 'range' => [
                self::FUNCAO_PROPRIETARIO,
                self::FUNCAO_GERENTE,
                self::FUNCAO_VENDEDOR
            ]],
            [['ultimo_acesso_em', 'criado_em', 'atualizado_em'], 'safe'],
            [['usuario_id', 'loja_id'], 'unique', 'targetAttribute' => ['usuario_id', 'loja_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_id' => 'Usuário',
            'loja_id' => 'Loja',
            'funcao' => 'Função',
            'status' => 'Status',
            'permissoes' => 'Permissões',
            'ultimo_acesso_em' => 'Último Acesso',
            'criado_em' => 'Criado em',
            'atualizado_em' => 'Atualizado em',
        ];
    }

    // ==================== RELACIONAMENTOS ====================

    /**
     * Relacionamento com o LojistaUsuario
     */
    public function getUsuario()
    {
        return $this->hasOne(LojistaUsuario::class, ['id' => 'usuario_id']);
    }

    /**
     * Relacionamento com a Loja (model do gestor)
     */
    public function getLoja()
    {
        return $this->hasOne(Loja::class, ['id' => 'loja_id']);
    }

    // ==================== MÉTODOS AUXILIARES ====================

    /**
     * Verifica se a associação está ativa
     */
    public function isAtivo()
    {
        return $this->status == self::STATUS_ATIVO;
    }

    /**
     * Ativa a associação
     */
    public function ativar()
    {
        $this->status = self::STATUS_ATIVO;
        return $this->save(false);
    }

    /**
     * Inativa a associação
     */
    public function inativar()
    {
        $this->status = self::STATUS_INATIVO;
        return $this->save(false);
    }

    /**
     * Retorna o nome da função em português
     */
    public function getFuncaoLabel()
    {
        $labels = [
            self::FUNCAO_PROPRIETARIO => 'Proprietário',
            self::FUNCAO_GERENTE => 'Gerente',
            self::FUNCAO_VENDEDOR => 'Vendedor',
        ];
        return $labels[$this->funcao] ?? $this->funcao;
    }

    /**
     * Retorna o status em texto
     */
    public function getStatusLabel()
    {
        return $this->status == self::STATUS_ATIVO ? 'Ativo' : 'Inativo';
    }
}