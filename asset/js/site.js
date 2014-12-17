var session_id = null;
var login_passed = false;
var servers = [];

$(document).ready(function() {
    api_present_loop();
    loading_overlay_init();
    loading_overlay_set(true);
    hook_login_forms();
});

function hook_login_forms() {
    $("#form-login").submit(function(e) {
        e.preventDefault();
        api_call("login", {username: $("#input-login-username").val(), password: $("#input-login-password").val()}, function(data) {
            if (session_id === null) {
                session_id = data.SessionId;
                login_passed = true;
                screen_show("chat");
                api_present_loop();
            }
        }, function(data) {
            if (data === "InvalidCredentials") {
                error_show("form-login-error", "Login failed. Please check your username and password and try again.");
                return;
            }
            alert(data);
        });
    });
}

function api_present_loop() {
    api_call("present", null, function(data) {
        if (!login_passed) {
            loading_overlay_set(false);
            screen_show("chat");
            login_passed = true;
        }
        setTimeout("api_present_loop();", 1000);
    }, function(data) {
        if (data === "Unauthorized") {
            loading_overlay_set(false);
            screen_show("login");
            return;
        }
        alert(data);
    });
}

function api_call(query_method, params, ok_fn, error_fn) {
    $.post("/api.php?_q=" + query_method + ((session_id === null) ? "" : "&_s=" + session_id), params, function(data) {
        if (data.Status === false) {
            error_fn(data.Error);
        } else {
            if (data.ServerInfo !== undefined && data.ServerInfo !== null) {
                var processed = false;
                for (var i in servers) {
                    var s = servers[i];
                    if (s.Hostname === data.ServerInfo.Hostname) {
                        s.Count++;
                        s.LoadAverage = data.ServerInfo.LoadAverage;
                        processed = true;
                        break;
                    }
                }
                if (!processed) {
                    var s = data.ServerInfo;
                    s.Count = 1;
                    servers.push(s);
                }
            }
            var servers_html = "";
            for (var i in servers) {
                var s = servers[i];
                servers_html += "<li role=\"presentation\" data-server-hostname=\"" + s.Hostname + "\"><a href=\"#\"><div class=\"row\"><div class=\"col-sm-3\"><span style=\"font-size: 24px; padding-top: 18px;\" class=\"glyphicon glyphicon-cloud\"></span></div><div class=\"col-sm-9\">";
                servers_html += "<strong>" + s.Hostname + "</strong> <span style=\"font-size: 10px; line-height: 10px; padding: 0px; margin: 0px;\">" + s.IpAddress + "</span><br/>";
                servers_html += "<span style=\"font-size: 10px; line-height: 10px; padding: 0px; margin: 0px;\">" + s.LoadAverage.join(" ") + "</span><br/>";
                servers_html += "<span style=\"font-size: 10px; line-height: 10px; padding: 0px; margin: 0px;\">" + s.Count + " requests processed</span><br/>";
                servers_html += "</div></div></a></li>";
            }
            $("#server-stats").html(servers_html);
            $("#server-stats li[data-server-hostname='" + data.ServerInfo.Hostname + "'] a").animate({
                color: "#5cb85c"
            }, 100);
            setTimeout(function() {
                $("#server-stats li[data-server-hostname='" + data.ServerInfo.Hostname + "'] a").animate({
                    color: "#337ab7;"
                }, 1000);
            }, 120);
            ok_fn(data.Response);
        }
    }, "json");
}

function error_show(id, text) {
    $("div#" + id + " .error-text").html(text);
    $("div#" + id).show();
}

function screen_show(name) {
    var visible = $("div[data-screen-name='" + name + "']").is(":visible");
    if (!visible) {
        $("div[data-screen-name]").each(function() {
            $(this).fadeOut(500);
        });
        setTimeout(function() {
            $("div[data-screen-name='" + name + "']").fadeIn(500);
        }, 500);
    }
}

function loading_overlay_init() {
    var ww = $(window).width();
    var wh = $(window).height();
    var ew = $("div.loading-overlay-progress").outerWidth();
    var eh = $("div.loading-overlay-progress").outerHeight();
    $("div.loading-overlay-progress").offset({left: (ww / 2 - ew / 2), top: (wh / 2 - eh / 2)});
}

function loading_overlay_set(state) {
    var visible = $("div.loading-overlay").is(":visible");
    if (visible && !state) {
        $("div.loading-overlay").fadeOut(200);
        $("div.loading-overlay-progress").fadeOut(200);
    } else if (!visible && state) {
        $("div.loading-overlay").fadeIn(200);
        $("div.loading-overlay-progress").fadeIn(200);
    }
}