<?php include "./inc/init.inc.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="asset/bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="asset/css/site.css" />
        <script type="text/javascript" src="asset/js/jquery.js"></script>
        <script type="text/javascript" src="asset/js/jquery.color.js"></script>
        <script type="text/javascript" src="asset/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="asset/js/site.js"></script>
        <title>Load-balanced Chat Example</title>
    </head>
    <body>
        <div class="loading-overlay" style="display: none;"></div>
        <div class="loading-overlay-progress" style="display: none;">
            <h4><i class="glyphicon glyphicon-cloud"></i> Loading. Please wait...</h4>
            <div class="progress">
                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                    <span class="sr-only">Loading</span>
                </div>
            </div>
        </div>

        <div class="container" style="max-width: 980px; margin-top: 15px;">
            <nav class="navbar navbar-default" role="navigation">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="#">
                            <span class="glyphicon glyphicon-comment" />
                        </a>
                    </div>
                </div>
            </nav>
            <div class="container-fluid">
                <div data-screen-name="welcome">
                    <h1>Loading...</h1>
                </div>
                <div data-screen-name="login" style="display: none">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <h1><span class="glyphicon glyphicon-lock"></span> Sign in</h1>
                            <div class="alert alert-danger" role="alert" id="form-login-error" style="display: none">
                                <strong>Shit!</strong> <span class="error-text"></span>
                            </div>
                            <form class="form-horizontal" role="form" id="form-login">
                                <div class="form-group">
                                    <label for="input-login-username" class="col-sm-2 control-label">Username</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="input-login-username" placeholder="Your username">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="input-login-password" class="col-sm-2 control-label">Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" id="input-login-password" placeholder="Your password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-primary">Sign in</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="border-left: 1px #cccccc solid;">
                            <h1><span class="glyphicon glyphicon-asterisk"></span> Register</h1>
                            <div class="alert alert-danger" role="alert" id="form-register-error" style="display: none">
                                <strong>Shit!</strong> <span class="error-text"></span>
                            </div>
                            <div class="alert alert-success" role="alert" id="form-register-success" style="display: none">
                                <strong>Success!</strong> <span class="error-text"></span>
                            </div>
                            <form class="form-horizontal" role="form" id="form-register">
                                <div class="form-group">
                                    <label for="input-register-username" class="col-sm-4 control-label">Username</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="input-register-username" placeholder="Your new username">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="input-register-password" class="col-sm-4 control-label">Password</label>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" id="input-register-password" placeholder="Your new password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="input-register-password2" class="col-sm-4 control-label">Confirm Password</label>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" id="input-register-password2" placeholder="Your new password again">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                        <button type="submit" class="btn btn-success">Register</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div data-screen-name="chat" style="display: none">
                    <ul class="nav nav-pills" id="server-stats">
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer text-center"><div class="well" style="text-align: center"><strong>&copy;2014 Ľubomír Kaplán &lt;<a href="mailto:castor@castor.sk">castor@castor.sk</a>&gt;</strong><br/>
                This site is developed only as a load-balanced chat demonstration tool and is not suitable for production use.</div></div>
    </body>
</html>