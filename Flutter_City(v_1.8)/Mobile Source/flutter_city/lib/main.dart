import 'dart:async';
import 'dart:io';
import 'package:easy_localization/easy_localization.dart';
// import 'package:admob_flutter/admob_flutter.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_crashlytics/firebase_crashlytics.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:fluttercity/config/ps_theme_data.dart';
import 'package:fluttercity/config/router.dart' as router;
import 'package:fluttercity/provider/ps_provider_dependencies.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/common/language.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
//import 'package:in_app_purchase_android/in_app_purchase_android.dart';
//import 'package:in_app_purchase_ios/in_app_purchase_ios.dart';
import 'package:provider/provider.dart';
import 'package:provider/single_child_widget.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:theme_manager/theme_manager.dart';
import 'config/ps_colors.dart';
import 'config/ps_config.dart';

Future<void> main() async {
  // add this, and it should be the first line in main method
  WidgetsFlutterBinding.ensureInitialized();

  // final FirebaseMessaging _fcm = FirebaseMessaging.instance;
  // if (Platform.isIOS) {
  //   _fcm.requestNotificationPermissions(const IosNotificationSettings());
  // }

  final SharedPreferences prefs = await SharedPreferences.getInstance();

  if (prefs.getString('codeC') == null) {
    await prefs.setString('codeC', ''); //null);
    await prefs.setString('codeL', ''); //null);
  }

  // Admob.initialize(Utils.getAdAppId());

  await Firebase.initializeApp();
  //NativeAdmob(adUnitID: Utils.getAdAppId());

  FlutterError.onError = FirebaseCrashlytics.instance.recordFlutterFatalError;

   if (Platform.isIOS) {
    FirebaseMessaging.instance.requestPermission(
          alert: true,
          announcement: false,
          badge: true,
          carPlay: false,
          criticalAlert: false,
          provisional: false,
          sound: true,
      );
  }
  /// Update the iOS foreground notification presentation options to allow
  /// heads up notifications.
  await FirebaseMessaging.instance.setForegroundNotificationPresentationOptions(
    alert: true,
    badge: true,
    sound: true,
  );

  //check is apple signin is available
  await Utils.checkAppleSignInAvailable();

  // Inform the plugin that this app supports pending purchases on Android.
  // An error will occur on Android if you access the plugin `instance`
  // without this call.
  //
  // On iOS this is a no-op.
  // if (Platform.isAndroid) {
  //   InAppPurchaseAndroidPlatformAddition.enablePendingPurchases();
  // }else {
  //    InAppPurchaseIosPlatform.registerPlatform();	
  // }
  
  MobileAds.instance.initialize();

  runApp(EasyLocalization(
      path: 'assets/langs',
      saveLocale: true,
      startLocale: PsConfig.defaultLanguage.toLocale(),
      supportedLocales: getSupportedLanguages(),
      child: PSApp()));
}

List<Locale> getSupportedLanguages() {
  final List<Locale> localeList = <Locale>[];
  for (final Language lang in PsConfig.psSupportedLanguageList) {
    localeList.add(Locale(lang.languageCode!, lang.countryCode));
  }
  print('Loaded Languages');
  return localeList;
}

class PSApp extends StatefulWidget {
  @override
  _PSAppState createState() => _PSAppState();
}

Future<dynamic> initAds() async {
  if (PsConfig.showAdMob && await Utils.checkInternetConnectivity()) {
    // FirebaseAdMob.instance.initialize(appId: Utils.getAdAppId());
  }
}

class _PSAppState extends State<PSApp> {
 
  @override
  Widget build(BuildContext context) {
    // init Color
    PsColors.loadColor(context);
     Utils.psPrint(EasyLocalization.of(context)!.locale.languageCode);

    return MultiProvider(
        providers: <SingleChildWidget>[
          ...providers,
        ],
        child: ThemeManager(
            defaultBrightnessPreference: BrightnessPreference.light,
            data: (Brightness brightness) {
              if (brightness == Brightness.light) {
                return themeData(ThemeData.light());
              } else {
                return themeData(ThemeData.dark());
              }
            },
            themedWidgetBuilder: (BuildContext context, ThemeData theme) {
              return MaterialApp(
                debugShowCheckedModeBanner: false,
                title: 'SENF-CITY',
                theme: theme,
                initialRoute: '/',
                onGenerateRoute: router.generateRoute,
                localizationsDelegates: <LocalizationsDelegate<dynamic>>[
                  GlobalMaterialLocalizations.delegate,
                  GlobalWidgetsLocalizations.delegate,
                  EasyLocalization.of(context)!.delegate,
                ],
                supportedLocales: EasyLocalization.of(context)!.supportedLocales,
                locale: EasyLocalization.of(context)!.locale,
              );
            }));
  }
}