import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/constant/route_paths.dart';
import 'package:fluttercity/provider/language/language_provider.dart';
import 'package:fluttercity/repository/language_repository.dart';
import 'package:fluttercity/ui/common/ps_admob_banner_widget.dart';
import 'package:fluttercity/ui/common/ps_dropdown_base_widget.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/common/language.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:provider/provider.dart';

class LanguageSettingView extends StatefulWidget {
  const LanguageSettingView(
      {Key? key,
      required this.animationController,
      required this.languageIsChanged})
      : super(key: key);
  final AnimationController animationController;
  final Function languageIsChanged;
  @override
  _LanguageSettingViewState createState() => _LanguageSettingViewState();
}

class _LanguageSettingViewState extends State<LanguageSettingView> {
  String currentLang = '';
  LanguageRepository? repo1;

  bool isConnectedToInternet = false;
  bool isSuccessfullyLoaded = true;

  void checkConnection() {
    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
      if (isConnectedToInternet && PsConfig.showAdMob) {
        if (mounted) {
          setState(() {});
        }
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    if (!isConnectedToInternet && PsConfig.showAdMob) {
      print('loading ads....');
      checkConnection();
    }
    final Animation<double> animation = Tween<double>(begin: 0.0, end: 1.0)
        .animate(CurvedAnimation(
            parent: widget.animationController,
            curve: const Interval(0.5 * 1, 1.0, curve: Curves.fastOutSlowIn)));
    widget.animationController.forward();
    final LanguageRepository repo1 = Provider.of<LanguageRepository>(context);

    return ChangeNotifierProvider<LanguageProvider>(
      lazy: false,
      create: (BuildContext context) {
        final LanguageProvider provider = LanguageProvider(repo: repo1);
        provider.getLanguageList();
        return provider;
      },
      child: Consumer<LanguageProvider>(builder:
          (BuildContext context, LanguageProvider provider, Widget? child) {
        return AnimatedBuilder(
            animation: widget.animationController,
            child: SingleChildScrollView(
                child: Container(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.start,
                mainAxisSize: MainAxisSize.max,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: <Widget>[
                  Container(
                    width: MediaQuery.of(context).size.width,
                    height: MediaQuery.of(context).size.height / 3,
                    padding: const EdgeInsets.all(PsDimens.space4),
                    decoration: BoxDecoration(
                      color: Utils.isLightMode(context)
                          ? PsColors.mainColor
                          : Colors.black12,
                      // Utils.isLightMode
                      //     ? Colors.black12
                      //     : PsColors.mainColor
                    ),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: <Widget>[
                        Image.asset(
                          'assets/images/arm.png',
                          width: PsDimens.space100,
                          height: PsDimens.space72,
                        ),
                        // Container(
                        //   width: 90,
                        //   height: 90,
                        //   child: Image.asset(
                        //     'assets/images/arm.png',
                        //   ),
                        // ),
                        const SizedBox(
                          height: PsDimens.space8,
                        ),
                        Text(
                          Utils.getString('app_name'),
                          style: Theme.of(context)
                              .textTheme
                              .headline5!
                              .copyWith(color: Colors.white),
                        ),
                      ],
                    ),
                  ),
                  PsDropdownBaseWidget(
                      title: Utils.getString(''),
                      selectedText: provider.getLanguage().name,
                      onTap: () async {
                        final dynamic result = await Navigator.pushNamed(
                            context, RoutePaths.languageList);
                        if (result != null && result is Language) {
                          // EasyLocalization.of(context).set
                          // await data.changeLocale(result.toLocale());

                          // EasyLocalization.of(context).

                          await provider.addLanguage(result);
                          // EasyLocalization.of(context).locale =
                          //     Locale(result.languageCode, result.countryCode);
                          // Locale(result.languageCode, result.countryCode);
                          EasyLocalization.of(context)?.setLocale( Locale(result.languageCode!, result.countryCode));
                        }
                        Utils.psPrint(result.toString());
                      }),
                  const PsAdMobBannerWidget(
                    admobSize: AdSize.fullBanner,
                    // admobBannerSize: AdmobBannerSize.MEDIUM_RECTANGLE,
                  ),
                ],
              ),
            )),
            builder: (BuildContext context, Widget? child) {
              return FadeTransition(
                  opacity: animation,
                  child: Transform(
                      transform: Matrix4.translationValues(
                          0.0, 100 * (1.0 - animation.value), 0.0),
                      child: child));
            });
      }),
    );
  }
}
