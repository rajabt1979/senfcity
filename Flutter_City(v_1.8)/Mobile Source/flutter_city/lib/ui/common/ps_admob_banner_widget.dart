// import 'dart:async';

// import 'package:flutter/material.dart';
// import 'package:flutter_native_admob/flutter_native_admob.dart';
// import 'package:flutter_native_admob/native_admob_controller.dart';
// import 'package:flutter_native_admob/native_admob_options.dart';
// import 'package:fluttercity/config/ps_config.dart';
// import 'package:fluttercity/constant/ps_dimens.dart';
// import 'package:fluttercity/utils/utils.dart';

// class PsAdMobBannerWidget extends StatefulWidget {
//   const PsAdMobBannerWidget({this.admobSize = NativeAdmobType.banner});

//   final NativeAdmobType admobSize;
//   // final AdmobBannerSize admobBannerSize;

//   @override
//   _PsAdMobBannerWidgetState createState() => _PsAdMobBannerWidgetState();
// }

// class _PsAdMobBannerWidgetState extends State<PsAdMobBannerWidget> {
//   bool isShouldLoadAdMobBanner = true;
//   bool isConnectedToInternet = false;
//   int currentRetry = 0;
//   int retryLimit = 1;
//   // ignore: always_specify_types
//   StreamSubscription _subscription;
//   final NativeAdmobController _controller = NativeAdmobController();
//   double _height = 0;

//   @override
//   void initState() {
//     _subscription = _controller.stateChanged.listen(_onStateChanged);
//     super.initState();
//   }

//   @override
//   void dispose() {
//     _subscription.cancel();
//     super.dispose();
//   }

//   void checkConnection() {
//     Utils.checkInternetConnectivity().then((bool onValue) {
//       isConnectedToInternet = onValue;
//       if (isConnectedToInternet && PsConfig.showAdMob) {
//         setState(() {});
//       }
//     });
//   }

//   void _onStateChanged(AdLoadState state) {
//     switch (state) {
//       case AdLoadState.loading:
//         setState(() {
//           _height = 0;
//         });
//         break;

//       case AdLoadState.loadCompleted:
//         setState(() {
//           if (widget.admobSize == NativeAdmobType.banner) {
//             _height = 80;
//           } else {
//             _height = 330;
//           }
//         });
//         break;

//       default:
//         break;
//     }
//   }

//   @override
//   Widget build(BuildContext context) {
//     return Container(
//         height: _height,
//         padding: const EdgeInsets.all(PsDimens.space16),
//         child: NativeAdmob(
//           adUnitID: Utils.getBannerAdUnitId(),
//           loading: Container(),
//           error: Container(),
//           controller: _controller,
//           type: widget.admobSize,
//           options: const NativeAdmobOptions(
//             ratingColor: Colors.red,
//           ),
//         ));
//   }
// }

import 'package:flutter/material.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';

class PsAdMobBannerWidget extends StatefulWidget {
  const PsAdMobBannerWidget({required this.admobSize});

  final AdSize? admobSize;

  @override
  _PsAdMobBannerWidgetState createState() => _PsAdMobBannerWidgetState();
}

class _PsAdMobBannerWidgetState extends State<PsAdMobBannerWidget> {
  bool showAds = false;
  bool isConnectedToInternet = false;
  int currentRetry = 0;
  int retryLimit = 1;

  BannerAd? _bannerAd;
  double height = 0;

  @override
  void initState() {
    _bannerAd = BannerAd(
      adUnitId: Utils.getBannerAdUnitId(),
      request: const AdRequest(),
      size: widget.admobSize!,
      listener: BannerAdListener(
        onAdLoaded: (Ad ad) {
          showAds = true;
          setState(() {
            if (widget.admobSize == AdSize.banner) {
              height = 60;
            } else {
              height = 250;
            }
          });
          print('loaded');
        },
        onAdFailedToLoad: (Ad ad, LoadAdError error) {
          print('$BannerAd failedToLoad: $error');
          showAds = false;
        },
        onAdOpened: (Ad ad) {
          print('$BannerAd onAdOpened.');
        },
        onAdClosed: (Ad ad) {
          print('$BannerAd onAdClosed.');
        },
      ),
    );
    _bannerAd!.load();
    super.initState();
  }

  @override
  void dispose() {
    super.dispose();
  }

  // void checkConnection() {
  //   Utils.checkInternetConnectivity().then((bool onValue) {
  //     isConnectedToInternet = onValue;
  //     if (isConnectedToInternet && PsConfig.showAdMob) {
  //       setState(() {});
  //     }
  //   });
  // }

  @override
  Widget build(BuildContext context) {
    return Container(
      alignment: Alignment.center,
      child: showAds && PsConfig.showAdMob ? AdWidget(ad: _bannerAd!) : Container(),
      width: _bannerAd!.size.width.toDouble(),
      height: _bannerAd!.size.height.toDouble(),
    );
  }
}

