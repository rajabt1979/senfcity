import 'dart:math';
import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/constant/ps_constants.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/ui/common/base/ps_widget_with_appbar_with_no_provider.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/map_pin_call_back_holder.dart';
//import 'package:geocode/geocode.dart';
import 'package:latlong2/latlong.dart';

class MapPinView extends StatefulWidget {
  const MapPinView(
      {required this.flag, required this.maplat, required this.maplng});

  final String? flag;
  final String? maplat;
  final String? maplng;

  @override
  _MapPinViewState createState() => _MapPinViewState();
}

class _MapPinViewState extends State<MapPinView> with TickerProviderStateMixin {
  LatLng? latlng;
  double defaultRadius = 3000;
  String address = '';

  dynamic loadAddress() async {
    // try {
    //   final Address? locationAddress = await GeoCode().reverseGeocoding(
    //       latitude: latlng!.latitude,
    //       longitude: latlng!.longitude);
    //
    //   if (locationAddress != null) {
    //     if (locationAddress.streetAddress != null &&
    //         locationAddress.countryName != null) {
    //       address =
    //       '${locationAddress.streetAddress} \n, ${locationAddress.countryName}';
    //     } else {
    //       address = locationAddress.region!;
    //     }
    //   }
    // } catch (e) {
    //   address = '';
    //   print(e);
    // }
  }

  @override
  Widget build(BuildContext context) {
    latlng ??= LatLng(double.parse(widget.maplat!), double.parse(widget.maplng!));

    final double scale = defaultRadius / 200; //radius/20
    final double value = 16 - log(scale) / log(2);
    loadAddress();

    print('value $value');

    return PsWidgetWithAppBarWithNoProvider(
        appBarTitle: Utils.getString( 'map_pin__title'),
        actions: widget.flag == PsConst.PIN_MAP
            ? <Widget>[
                InkWell(
                  child: Ink(
                    child: Center(
                      child: Text(
                        'همینجا ست شود',
                        textAlign: TextAlign.justify,
                        style: Theme.of(context)
                            .textTheme
                            .bodyMedium!
                            .copyWith(fontWeight: FontWeight.bold)
                            .copyWith(color: PsColors.mainColor),
                      ),
                    ),
                  ),
                  onTap: () {
                    Navigator.pop(context,
                        MapPinCallBackHolder(address: address, latLng: latlng!));
                  },
                ),
                const SizedBox(
                  width: PsDimens.space16,
                ),
              ]
            : <Widget>[],
        child: Scaffold(
          body: Column(
            children: <Widget>[
              Flexible(
                child: FlutterMap(
                  options: MapOptions(
                      center:
                      latlng, //LatLng(51.5, -0.09), //LatLng(45.5231, -122.6765),
                      zoom: value, //10.0,
                      onTap: widget.flag == PsConst.PIN_MAP
                          ? _handleTap
                          : _doNothingTap),
                  children: <Widget>[
                    TileLayer(
                      urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                    ),
                    MarkerLayer(markers: <Marker>[
                      Marker(
                        width: 80.0,
                        height: 80.0,
                        point: latlng!,
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
            ],
          ),
        ));
  }

  void _handleTap(dynamic tapPosition,LatLng latlng) {
    setState(() {
      this.latlng = latlng;
    });
  }

  void _doNothingTap(dynamic tapPosition,LatLng latlng) {}
}
