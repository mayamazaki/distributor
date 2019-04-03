<?php use Cake\Core\Configure; ?>
<div class="content-box-large">
	<div class="panel-heading">
		<div class="panel-title">ライセンス使用状況</div>
		<div class="panel-options">
			<?php echo $this->Form->create('licensePartners', ['url' => ['controller' => 'licensePartners', 'action' => 'csv'], 'type' => 'get', 'class' => 'form-inline', 'novalidate']); ?>
				<?= $this->Form->control('param_cram_school_id', ['type' => 'hidden', 'value' => $cramSchoolId]); ?>
				<?= $this->Form->control('param_class_id', ['type' => 'hidden', 'value' => $classId]); ?>
				<button class="btn btn-success btn csv" type="submit">CSV出力</button>
			<?= $this->Form->end(); ?>
		</div>
	</div>

	<div class="panel-body">

		<?php echo $this->Form->create('licensePartners', ['url' => ['controller' => 'licensePartners', 'action' => 'index'], 'type' => 'get', 'class' => 'form-inline', 'novalidate']); ?>

			<div class="row">
				<div class="col-md-12">
					<div class="row margin">
						<div class="col-md-2">
							<div class="form-group">
								<label for="">塾　</label>
								<?php echo $this->Form->input('cram_school_id', ['id' => 'cramSchoolId', 'label' => false, 'type' => 'select', 'empty' => 'すべて', 'required' => false, 'class' => 'form-control cram-schools', 'error' => false, 'options' => $cramSchools, 'value' => $cramSchoolId, 'style' => 'width:150px']); ?>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="">クラス</label>
								<?php echo $this->Form->input('class_id', ['id' => 'classId', 'label' => false, 'type' => 'select', 'empty' => 'すべて', 'required' => false, 'class' => 'form-control cram-school-classes', 'error' => false, 'options' => $cramSchoolClasses, 'value' => $classId, 'style' => 'width:150px']); ?>
							</div>
						</div>
					</div>
					<div class="row margin">
						<div class="col-md-3">
							<div class="form-group">
								<br />
								<button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i>検索</button>
							</div>
						</div>
					</div>
				</div>
			</div>

		<?= $this->Form->end(); ?>

		<div class="row">
			<div class="col-xs-6">
				<div class="dataTables_info" id="example_info">
					<?= $this->Paginator->counter([
								'format' => __('{{start}}〜{{end}}件目 / 全{{count}}件')
						]);
					?>
				</div>
			</div>
		</div>

		<div class="table-responsive">
			<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered">
				<thead>
					<tr style="background-color:#E8F3FF;">
						<th>ID</th>
						<th>表示順</th>
						<th>有効/無効</th>
						<th>ライセンスコード</th>
						<th>ライセンス有効期間（開始日）</th>
						<th>ライセンス有効期間（終了日）</th>
						<th>認証日時</th>
						<th>塾</th>
						<th>クラス</th>
						<th>ユーザーID</th>
						<th>ユーザー名</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($licensePartners as $licensePartner): ?>
					<tr>
						<td><?= $this->Number->format($licensePartner->id) ?></td>
						<td><?= $this->Number->format($licensePartner->disp_no) ?></td>
						<td><?= Configure::read('is_valid')[$this->Number->format($licensePartner->is_valid)] ?></td>
						<td><?= h($licensePartner->license_code) ?></td>
						<td><?= h($licensePartner->exp_s_dt) ?></td>
						<td><?= h($licensePartner->exp_f_dt) ?></td>
						<td><?= h($licensePartner->auth_datetime) ?></td>
						<td><?= $licensePartner->user->has('cram_school') ? h($licensePartner->user->cram_school->name) : '' ?></td>
						<td><?= $licensePartner->user->student->cram_school_class ? h($licensePartner->user->student->cram_school_class->name) : '' ?></td>
						<td><?= $licensePartner->has('user') ? h($licensePartner->user->id) : '' ?></td>
						<td><?= $licensePartner->has('user') ? h($licensePartner->user->name) : '' ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<div class="row text-center">
				<div class="dataTables_paginate paging_bootstrap">
					<ul class="pagination">
						<?php echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a')); ?>
						<?php echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1, 'ellipsis' => '<li class="disabled"><a>...</a></li>')); ?>
						<?php echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a')); ?>
					</ul>
				</div>
			</div>

			<div id="dialog" style="display: none"></div>
		</div>
	</div>
</div>

<!-- Get Classes Form -->
<form id="getClassesForm" method="post" action="<?= $this->Url->build(["controller" => "licensePartners", "action" => "getClasses"], true); ?>">

<?= $this->Html->scriptStart(array('inline' => true)); ?>

$(function(){

	/* user selectbox change */
	$(document).on('change', '.cram-schools', function(){

		var cramSchoolId = $(this).val();

		var $form = $('#getClassesForm');
		$.ajax({
			url: $form.attr('action'),
			type: $form.attr('method'),
			data: {
				cram_school_id: cramSchoolId,
			},
			success: function(response) {
				var data = JSON.parse(response);
				$('.cram-school-classes option').remove();
				$('.cram-school-classes').append($('<option>').text('すべて').attr('value', ''));
				$.each(data.cram_school_classes, function(id, name){
					$('.cram-school-classes').append($('<option>').text(name).attr('value', id));
				});
			}
		});
	});

});

<?= $this->Html->scriptEnd(); ?>
