<?= $this->layout('admin/layout'); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Main content -->
    <section class="content container-fluid">

        <!-- Main content -->
        <section class="content">

            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Админ-панель</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                title="Collapse">
                            <i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
                            <i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="">
                        <div class="box-header">
                            <h2 class="box-title">Все категории</h2>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <a href="/admin/categories/create" class="btn btn-success btn-lg">Добавить</a> <br> <br>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 10%;">Номер</th>
                                    <th>Название</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($categories as $category) {?>
                                    <tr>
                                        <td><?= $category['id'];?></td>
                                        <td><?= $category['title'];?></td>
                                        <td>
                                            <a href="/admin/categories/<?= $category['id'];?>/edit" class="btn btn-warning">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a href="/admin/categories/<?= $category['id'];?>/delete" class="btn btn-danger" onclick="return confirm('Вы уверены?');">
                                                <i class="fa fa-remove"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </section>
</div>
