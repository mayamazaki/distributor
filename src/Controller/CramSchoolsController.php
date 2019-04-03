<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;

/**
 * CramSchools Controller
 *
 * @property \App\Model\Table\CramSchoolsTable $CramSchools
 *
 * @method \App\Model\Entity\CramSchool[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CramSchoolsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'conditions' => [
                'CramSchools.is_valid' => 1 // 1.有効
            ],
            'order' => [
                'CramSchools.disp_no' => 'asc' // 表示順
            ]
        ];
        $cramSchools = $this->paginate($this->CramSchools);

        $this->set(compact('cramSchools'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cramSchool = $this->CramSchools->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['host'] = $_SERVER['REMOTE_ADDR']; // IP
            $cramSchool = $this->CramSchools->patchEntity($cramSchool, $data);
            if ($this->CramSchools->save($cramSchool)) {
                $this->Flash->success(__('塾を登録しました。'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('入力項目にエラーがあります。'));
        }
        $this->set(compact('cramSchool'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Cram School id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cramSchool = $this->CramSchools->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $data['host'] = $_SERVER['REMOTE_ADDR']; // IP
            if (empty($data['password'])) {
                // パスワード未変更
                // パスワードのバリデーションを解除
                unset($data['password']);
                $this->CramSchools->getValidator('default')->offsetUnset('password');
            }
            $cramSchool = $this->CramSchools->patchEntity($cramSchool, $data);
            if ($this->CramSchools->save($cramSchool)) {
                $this->Flash->success(__('塾を更新しました。'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('入力項目にエラーがあります。'));
        }
        $this->set(compact('cramSchool'));
    }

    /**
     * Invalid method
     *
     * @param string|null $id Cram School id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function invalid($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->CramSchools->get($id);
        $data = [];
        $data['is_valid'] = 0; // 0.無効
        $user = $this->CramSchools->patchEntity($user, $data);
        if ($this->CramSchools->save($user)) {
            $this->Flash->success(__('塾を削除しました。'));
        } else {
            $this->Flash->error(__('塾の削除に失敗しました。'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
