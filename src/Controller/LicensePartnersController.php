<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\Core\Configure;

/**
 * LicensePartners Controller
 *
 * @property \App\Model\Table\LicensePartnersTable $LicensePartners
 *
 * @method \App\Model\Entity\LicensePartner[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LicensePartnersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        // 条件
        $conditions = [];

        $cramSchoolId = '';
        $classId = '';

        // 塾ID
        if (!empty($this->request->getQuery('cram_school_id'))) {
            $cramSchoolId = $this->request->getQuery('cram_school_id');
            $conditions += [
                'Users.cram_school_id' => $cramSchoolId
            ];
        }
        // クラスID
        if (!empty($this->request->getQuery('class_id'))) {
            $classId = $this->request->getQuery('class_id');
            $conditions += [
                'Students.cram_school_class_id' => $classId
            ];
        }

        $this->paginate = [
            // 'contain' => ['Licenses', 'Users' => ['CramSchools', 'Students' => 'CramSchoolClasses']],
            'contain' => ['Users' => ['CramSchools', 'Students' => 'CramSchoolClasses']],
            'conditions' => $conditions,
            'order' => [
                'LicensePartners.disp_no' => 'asc' // 表示順
            ]
        ];
        $licensePartners = $this->paginate($this->LicensePartners);

        // 塾
        $cramSchoolsTable = $this->getTableLocator()->get('CramSchools');
        $cramSchools = $cramSchoolsTable->find('list');
        // クラス
        $cramSchoolClasses = [];
        if (!empty($cramSchoolId)) {
            $cramSchoolClassesTable = $this->getTableLocator()->get('CramSchoolClasses');
            $cramSchoolClasses = $cramSchoolClassesTable->find('list')->where(['cram_school_id' => $cramSchoolId]);
        }

        $this->set(compact('licensePartners', 'cramSchools', 'cramSchoolClasses', 'cramSchoolId', 'classId'));
    }

	/**
	 * Export csv
	 *
	 * @return void
	 */
	public function csv() {

	    $cramSchoolId = $this->request->getQuery('param_cram_school_id');
	    $classId = $this->request->getQuery('param_class_id');

	    $where = [];
        // 塾ID
        if (!empty($cramSchoolId)) {
            $where += [
                'Users.cram_school_id' => $cramSchoolId
            ];
        }
        // クラスID
        if (!empty($classId)) {
            $where += [
                'Students.cram_school_class_id' => $classId
            ];
        }

        $licensePartners = $this->LicensePartners->find()
                // ->contain(['Licenses', 'Users' => ['CramSchools', 'Students' => 'CramSchoolClasses']])
                ->contain(['Users' => ['CramSchools', 'Students' => 'CramSchoolClasses']])
                ->where($where)
                ->order(['LicensePartners.id' => 'asc'])
                ->all();

        // ヘッダ
        $header = [
            'ID',
            '表示順',
            '有効/無効',
            'ライセンスコード',
            'ライセンス有効期間（開始日）',
            'ライセンス有効期間（終了日）',
            '認証日時',
            '塾',
            'クラス',
            'ユーザーID',
            'ユーザー名'
        ];

        $list = [];
		$list[] = $header;

        foreach ($licensePartners as $i => $licensePartner) {
            $list[] = [
                'id' => $licensePartner->id, // ID
                'disp_no' => $licensePartner->disp_no, // 表示順
                'is_valid' => Configure::read('is_valid')[$licensePartner->is_valid], // 有効/無効
                'license_code' => $licensePartner->license_code, // ライセンスコード
                'exp_s_dt' => $licensePartner->exp_s_dt->format('Y-n-j'), // ライセンス有効期間（開始日）
                'exp_f_dt' => $licensePartner->exp_f_dt->format('Y-n-j'), // ライセンス有効期間（終了日）
                'auth_datetime' => $licensePartner->auth_datetime->format('Y-n-j H:i:s'), // 認証日時
                'cram_school_name' => $licensePartner->user->has('cram_school') ? h($licensePartner->user->cram_school->name) : '', // 塾
                'cram_school_class_name' => $licensePartner->user->student->cram_school_class ? h($licensePartner->user->student->cram_school_class->name) : '', // クラス
                'user_id' => $licensePartner->has('user') ? h($licensePartner->user->id) : '', // ユーザーID
                'user_name' => $licensePartner->has('user') ? h($licensePartner->user->name) : '', // ユーザー名
            ];
        }

        // 文字化け対策
        foreach ($list as $lk => $lv) {
            foreach ($lv as $k => $v) {
                $list[$lk][$k] = mb_convert_encoding($v, 'SJIS', 'UTF8');
            }
        }

        $this->viewBuilder()->setLayout(false);
        $this->autoRender = false;
        $outputFileName = "ライセンス使用状況一覧_".date('YmdHis').".csv";
        $outputFileName = mb_convert_encoding($outputFileName, 'SJIS', 'UTF8');
        header("Content-type: plain/text; name={$outputFileName}");
        header("Content-Disposition: attachment;filename={$outputFileName}");
        $fp = fopen('php://output', 'w');
        foreach ($list as $line) {
            fputcsv($fp, $line);
        }
        fclose($fp);
        exit;

	}

    /**
     * クラス取得
     *
     * @param integer $cramSchoolId
     */
    public function getClasses() {

        $this->viewBuilder()->setLayout(false);
        $this->autoRender = false;
        if (!$this->request->is('ajax')) {
            throw new BadRequestException(__('Invalid'));
        }
        if (empty($this->request->getData('cram_school_id'))) {
            throw new NotFoundException(__('Invalid'));
        }

        // クラス
        $cramSchoolClassesTable = $this->getTableLocator()->get('CramSchoolClasses');
        $cramSchoolClasses = $cramSchoolClassesTable->find('list')->where([
            'CramSchoolClasses.cram_school_id' => $this->request->getData('cram_school_id'),
        ]);

        // ok
        $response = [
            'cram_school_classes' => $cramSchoolClasses,
        ];
        echo json_encode($response);
        return;

    }

}
