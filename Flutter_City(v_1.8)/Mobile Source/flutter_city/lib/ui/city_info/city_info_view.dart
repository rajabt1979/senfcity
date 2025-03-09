import 'dart:math';

import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/ui/common/ps_admob_banner_widget.dart';
// import 'package:fluttercity/ui/common/ps_admob_banner_widget.dart';
import 'package:fluttercity/ui/common/ps_ui_widget.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/city_info.dart';
import 'package:fluttercity/viewobject/default_photo.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:latlong2/latlong.dart';

class CityInfoView extends StatefulWidget {
  const CityInfoView(
      {Key? key,
        required this.cityInfo,
        this.animationController,
        this.animation})
      : super(key: key);

  final CityInfo cityInfo;
  final AnimationController? animationController;
  final Animation<double>? animation;

  @override
  _CityInfoViewState createState() => _CityInfoViewState();
}

class _CityInfoViewState extends State<CityInfoView> {
  @override
  Widget build(BuildContext context) {
    widget.animationController!.forward();
    return AnimatedBuilder(
        animation: widget.animationController!,
        child: _CityInfoViewWidget(widget: widget, cityInfo: widget.cityInfo),
        builder: (BuildContext context, Widget? child) {
          return FadeTransition(
              opacity: widget.animation!,
              child: Transform(
                transform: Matrix4.translationValues(
                    0.0, 30 * (1.0 - widget.animation!.value), 0.0),
                child: child,
              ));
        });
  }
}

class _CityInfoViewWidget extends StatefulWidget {
  const _CityInfoViewWidget({
    Key? key,
    required this.widget,
    required this.cityInfo,
  }) : super(key: key);

  final CityInfoView widget;
  final CityInfo cityInfo;

  @override
  __CityInfoViewWidgetState createState() => __CityInfoViewWidgetState();
}

class __CityInfoViewWidgetState extends State<_CityInfoViewWidget> {
  bool isConnectedToInternet = false;
  bool isSuccessfullyLoaded = true;

  void checkConnection() {
    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
      if (isConnectedToInternet && PsConfig.showAdMob) {
        setState(() {});
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    if (!isConnectedToInternet && PsConfig.showAdMob) {
      print('loading ads....');
      checkConnection();
    }
    return SingleChildScrollView(
      child: Column(
        children: <Widget>[
          const PsAdMobBannerWidget(admobSize: AdSize.banner,),
          _HeaderImageWidget(
            photo: widget.cityInfo.defaultPhoto!,
          ),
          Container(
            color: PsColors.coreBackgroundColor,
            margin: const EdgeInsets.only(
                left: PsDimens.space16, right: PsDimens.space16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: <Widget>[
                const SizedBox(
                  height: PsDimens.space16,
                ),
                Text(widget.cityInfo.name!,
                    style: Theme.of(context).textTheme.titleMedium!.copyWith(
                          color: PsColors.mainColor,
                        )),
                const SizedBox(
                  height: PsDimens.space16,
                ),
                Text(
                  widget.cityInfo.description!,
                  style: Theme.of(context)
                      .textTheme
                      .bodyMedium!
                      .copyWith(height: 1.3),
                ),
                const SizedBox(
                  height: PsDimens.space16,
                ),
                _SourceAddressWidget(
                  data: widget.cityInfo,
                ),
              ],
            ),
          )
        ],
      ),
    );
  }
}

class _HeaderImageWidget extends StatelessWidget {
  const _HeaderImageWidget({
    Key? key,
    required this.photo,
  }) : super(key: key);

  final DefaultPhoto photo;
  @override
  Widget build(BuildContext context) {
    return Stack(
      children: <Widget>[
        PsNetworkImage(
          photoKey: '',
          defaultPhoto: photo,
          width: double.infinity,
          height: 260,
          boxfit: BoxFit.cover,
          onTap: () {},
        ),
      ],
    );
  }
}

class _SourceAddressWidget extends StatelessWidget {
  const _SourceAddressWidget({
    Key? key,
    required this.data,
  }) : super(key: key);

  final CityInfo data;
  @override
  Widget build(BuildContext context) {
    LatLng? latlng;
    const double defaultRadius = 3000;
    // String address = '';

    //     dynamic loadAddress() async {
    //   final List<Address> addresses = await Geocoder.local
    //       .findAddressesFromCoordinates(
    //           Coordinates(latlng.latitude, latlng.longitude));
    //   // final Address first = addresses.first;
    //   // address = '${first.addressLine}  \n, ${first.countryName}';
    // }
    latlng ??= LatLng(double.parse(data.lat!), double.parse(data.lng!));
    const double scale = defaultRadius / 200; //radius/20
    final double value = 16 - log(scale) / log(2);
// loadAddress();
    return Container(
      color: PsColors.backgroundColor,
      child: Column(
        mainAxisAlignment: MainAxisAlignment.start,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: <Widget>[
          Text(
            Utils.getString('contact_info__location'),
            style: Theme.of(context).textTheme.titleMedium,
          ),
          const SizedBox(
            height: PsDimens.space16,
          ),
          Text(
            data.address!,
            style: Theme.of(context).textTheme.bodyLarge,
          ),
          const SizedBox(
            height: PsDimens.space16,
          ),
          Container(
            height: PsDimens.space200,
            child: FlutterMap(
              options: MapOptions(
                center:
                latlng, //LatLng(51.5, -0.09), //LatLng(45.5231, -122.6765),
                zoom: value, //10.0,
              ),
              children: <Widget>[
                TileLayer(
                  urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                ),
                MarkerLayer(markers: <Marker>[
                  Marker(
                    width: 80.0,
                    height: 80.0,
                    point: latlng,
                    builder: (BuildContext ctx) => Container(
                      child: IconButton(
                        icon: Icon(
                          Icons.location_on,
                          color: PsColors.mainColor,
                        ),
                        iconSize: 45,
                        onPressed: () {},
                      ),
                    ),
                  )
                ])
              ],
            ),
          ),
          const SizedBox(
            height: PsDimens.space16,
          ),
        ],
      ),
    );
  }
}
