import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/constant/route_paths.dart';
import 'package:fluttercity/provider/user/user_provider.dart';
import 'package:fluttercity/repository/user_repository.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttericon/font_awesome5_icons.dart';
import 'package:provider/provider.dart';

import 'register_view.dart';

class RegisterContainerView extends StatefulWidget {
  @override
  _CityRegisterContainerViewState createState() =>
      _CityRegisterContainerViewState();
}

class _CityRegisterContainerViewState extends State<RegisterContainerView>
    with SingleTickerProviderStateMixin {
  AnimationController? animationController;
  @override
  void initState() {
    animationController =
        AnimationController(duration: PsConfig.animation_duration, vsync: this);
    super.initState();
  }

  @override
  void dispose() {
    animationController!.dispose();
    super.dispose();
  }

  final GlobalKey<ScaffoldState> scaffoldKey = GlobalKey<ScaffoldState>();
  UserProvider? userProvider;
  UserRepository? userRepo;

  @override
  Widget build(BuildContext context) {
    Future<bool> _requestPop() {
      animationController!.reverse().then<dynamic>(
        (void data) {
          if (!mounted) {
            return Future<bool>.value(false);
          }
          Navigator.pop(context, true);
          return Future<bool>.value(true);
        },
      );
      return Future<bool>.value(false);
    }

    print(
        '............................Build UI Again ............................');
    userRepo = Provider.of<UserRepository>(context);
    return WillPopScope(
        onWillPop: _requestPop,
        child: Scaffold(
          body: Stack(children: <Widget>[
            Container(
              color: PsColors.mainLightColorWithBlack,
              width: double.infinity,
              height: double.maxFinite,
            ),
            CustomScrollView(
                scrollDirection: Axis.vertical,
                shrinkWrap: true,
                slivers: <Widget>[
                  _SliverAppbar(
                    title: Utils.getString('register__title'),
                    scaffoldKey: scaffoldKey,
                  ),
                  RegisterView(
                    animationController: animationController!,
                  ),
                ])
          ]),
        ));
  }
}

class _SliverAppbar extends StatefulWidget {
  const _SliverAppbar(
      {Key? key, required this.title, this.scaffoldKey, })
      : super(key: key);
  final String title;
  final GlobalKey<ScaffoldState>? scaffoldKey;
 // final Drawer? menuDrawer;
  @override
  _SliverAppbarState createState() => _SliverAppbarState();
}

class _SliverAppbarState extends State<_SliverAppbar> {
  @override
  Widget build(BuildContext context) {
    return SliverAppBar(
      systemOverlayStyle: SystemUiOverlayStyle(
        statusBarIconBrightness: Utils.getBrightnessForAppBar(context),
      ),
      backgroundColor:
          Utils.isLightMode(context) ? PsColors.mainColor : Colors.black12,
      iconTheme: Theme.of(context).iconTheme.copyWith(color: PsColors.white),
      title: Text(
        widget.title,
        textAlign: TextAlign.center,
        style: Theme.of(context)
            .textTheme
            .headline6!
            .copyWith(fontWeight: FontWeight.bold)
            .copyWith(color: PsColors.white),
      ),
      actions: <Widget>[
        IconButton(
          icon: Icon(Icons.notifications_none, color: PsColors.white),
          onPressed: () {
            Navigator.pushNamed(
              context,
              RoutePaths.notiList,
            );
          },
        ),
        IconButton(
          icon: Icon(FontAwesome5.book_open, color: PsColors.white),
          onPressed: () {
            Navigator.pushNamed(
              context,
              RoutePaths.blogList,
            );
          },
        )
      ],
      elevation: 0,
    );
  }
}
