        <style type="text/css">
            body {
              padding-top: 60px;
            }
        </style>
        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                  <a class="navbar-brand" href="javascript:;">管理面板</a>
                </div>
                <div class="navbar-collapse collapse">
                  <ul class="nav navbar-nav">
                    <?php foreach ($config['navbar_left'] as $href => $name): ?>
                    <?php if (BASENAME == $href): ?>
                    <li class="active">
                    <?php else: ?>
                    <li>
                    <?php endif; ?>
                        <a href="<?php echo $href; ?>"><?php echo $name; ?></a>
                    </li>
                    <?php endforeach; ?>
                  </ul>
                  <ul class="nav navbar-nav navbar-right">
                    <li><a href="javascript:;">您好：<?php echo $_SESSION['user']['username']; ?></a></li>
                    <?php foreach ($config['navbar_right'] as $href => $name): ?>
                    <?php if (BASENAME == $href): ?>
                    <li class="active">
                    <?php else: ?>
                    <li>
                    <?php endif; ?>
                        <a href="<?php echo $href; ?>"><?php echo $name; ?></a>
                    </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
            </div>
        </div>
