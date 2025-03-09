import 'dart:math';
import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/ui/common/base/ps_widget_with_appbar_with_no_provider.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/holder/item_parameter_holder.dart';
import 'package:latlong2/latlong.dart';

class MapFilterView extends StatefulWidget {
  const MapFilterView({required this.itemParameterHolder});

  final ItemParameterHolder itemParameterHolder;

  @override
  _MapFilterViewState createState() => _MapFilterViewState();
}

class _MapFilterViewState extends State<MapFilterView>
    with TickerProviderStateMixin {
  List<String> seekBarValues = <String>[
    '0.5',
    '1',
    '2.5',
    '5',
    '10',
    '25',
    '50',
    '100',
    '200',
    '500',
    'All'
  ];
  LatLng? latlng;
  final double zoom = 16;
  double radius = -1;
  double defaultRadius = 3000;
  bool isRemoveCircle = false;
  String address = '';
  bool isFirst = true;

  // dynamic loadAddress() async {
  //   final List<Address> addresses = await Geocoder.local
  //       .findAddressesFromCoordinates(
  //           Coordinates(latlng.latitude, latlng.longitude));
  //   final Address first = addresses.first;
  //   address =
  //       '${first.addressLine}  \n:  ${first.adminArea} \n: ${first.coordinates} \n: ${first.countryCode} \n: ${first.countryName} \n: ${first.featureName} \n: ${first.locality} \n: ${first.postalCode} \n: ${first.subLocality} \n: ${first.subThoroughfare} \n: ${first.thoroughfare}';
  //   print('${first.adminArea}  :  ${first.featureName} : ${first.addressLine}');
  // }

  double _value = 0.3;
  String kmValue = '5';

  int findTheIndexOfTheValue(String value) {
    int index = 0;

    for (int i = 0; i < seekBarValues.length - 1; i++) {
      if (!(value == 'All')) {
        if (getMiles(seekBarValues[i]) == value) {
          index = i;
          break;
        }
      } else {
        index = seekBarValues.length - 1;
      }
    }

    return index;
  }

  String getMiles(String kmValue) {
    final double _km = double.parse(kmValue);
    return (_km * 0.621371).toStringAsFixed(3);
  }

  @override
  Widget build(BuildContext context) {
    if (widget.itemParameterHolder.lat! == '')
      latlng = LatLng(0.0, 0.0);
    else
      latlng ??= LatLng(double.parse(widget.itemParameterHolder.lat!),
          double.parse(widget.itemParameterHolder.lng!));

    if (widget.itemParameterHolder.miles != '' && isFirst) {
      final int _index =
      findTheIndexOfTheValue(widget.itemParameterHolder.miles!);
      kmValue = seekBarValues[_index];
      final double _val = double.parse(getMiles(kmValue)) * 1000;
      radius = _val;
      defaultRadius = radius;
      _value = _index / 10;
      isFirst = false;
    }

    final double scale = defaultRadius / 300; //radius/20
    final double value = 16 - log(scale) / log(2);
    // loadAddress();

    print('value $value');

    final List<CircleMarker> circleMarkers = <CircleMarker>[
      CircleMarker(
          point: latlng!, //LatLng(51.5, -0.09),
          color: Colors.blue.withOpacity(0.7),
          borderStrokeWidth: 2,
          useRadiusInMeter: true,
          radius: isRemoveCircle == true
              ? 0.0
              : radius <= 0.0
              ? defaultRadius
              : radius //2000 // 2000 meters | 2 km//radius

      ),
    ];

    return PsWidgetWithAppBarWithNoProvider(
        appBarTitle: Utils.getString( 'map_filter__title'),
        actions: <Widget>[
          InkWell(
            child: Center(
              child: Text(
                Utils.getString( 'map_filter__reset'),
                textAlign: TextAlign.justify,
                style: Theme.of(context)
                    .textTheme
                    .bodyText2!
                    .copyWith(fontWeight: FontWeight.bold)
                    .copyWith(color: PsColors.mainColorWithWhite),
              ),
            ),
            onTap: () {
              setState(() {
                isRemoveCircle = true;
                _value = 1.0;
              });
            },
          ),
          const SizedBox(
            width: PsDimens.space20,
          ),
          InkWell(
            child: Center(
              child: Text(
                Utils.getString( 'map_filter__apply'),
                textAlign: TextAlign.center,
                style: Theme.of(context)
                    .textTheme
                    .bodyText2!
                    .copyWith(fontWeight: FontWeight.bold)
                    .copyWith(color: PsColors.mainColorWithWhite),
              ),
            ),
            onTap: () {
              if (kmValue == 'All') {
                widget.itemParameterHolder.lat = '';
                widget.itemParameterHolder.lng = '';
                widget.itemParameterHolder.miles = '';
              } else {
                widget.itemParameterHolder.lat = latlng!.latitude.toString();
                widget.itemParameterHolder.lng = latlng!.longitude.toString();
                widget.itemParameterHolder.miles = getMiles(kmValue);
              }
              Navigator.pop(context, widget.itemParameterHolder);
            },
          ),
          const SizedBox(
            width: PsDimens.space16,
          ),
        ],
        child: Scaffold(
          body: Column(
            children: <Widget>[
              Flexible(
                child: FlutterMap(
                  options: MapOptions(
                      center:
                      latlng, //LatLng(51.5, -0.09), //LatLng(45.5231, -122.6765),
                      zoom: value, //10.0,
                      onTap: _handleTap),
                  children: <Widget>[
                    TileLayer(
                        urlTemplate:
                        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                        subdomains: const <String>['a', 'b', 'c']),
                    CircleLayer(circles: circleMarkers),
                  ],
                ),
              ),
              Padding(
                padding: const EdgeInsets.only(
                  left: 16,
                  right: 16,
                  top: PsDimens.space8,
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: <Widget>[
                    Text(
                      Utils.getString( 'map_filter__browsing'),
                      textAlign: TextAlign.center,
                      style: Theme.of(context)
                          .textTheme
                          .bodyText2!
                          .copyWith(fontWeight: FontWeight.bold),
                    ),
                    Text(
                      Utils.getString( 'map_filter__all'),
                      textAlign: TextAlign.center,
                      style: Theme.of(context)
                          .textTheme
                          .bodyText2!
                          .copyWith(fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ),
              Container(
                child: Slider(
                  value: _value,
                  onChanged: (double newValue) {
                    setState(() {
                      _value = newValue;
                      kmValue = seekBarValues[(_value * 10).toInt()];
                      if (kmValue == 'All') {
                        isRemoveCircle = true;
                      } else {
                        radius = double.parse(getMiles(kmValue)) *
                            1000; //_value * 10000;
                      }
                      _value == 1
                          ? isRemoveCircle = true
                          : isRemoveCircle = false;
                      defaultRadius != 0
                          ? defaultRadius = 500
                          : defaultRadius = 500;
                    });
                  },
                  divisions: 10,
                  label: _value == 1
                      ? seekBarValues[(_value * 10).toInt()]
                      : seekBarValues[(_value * 10).toInt()] +
                      Utils.getString( 'map_filter__km'),
                ),
              ),
              Padding(
                padding: const EdgeInsets.only(
                  left: 16,
                  right: 16,
                  bottom: PsDimens.space8,
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: <Widget>[
                    Text(
                      Utils.getString( 'map_filter__lowest_km'),
                      textAlign: TextAlign.center,
                      style: Theme.of(context)
                          .textTheme
                          .bodyText2!
                          .copyWith(fontWeight: FontWeight.bold),
                    ),
                    Text(
                      Utils.getString( 'map_filter__all'),
                      textAlign: TextAlign.center,
                      style: Theme.of(context)
                          .textTheme
                          .bodyText2!
                          .copyWith(fontWeight: FontWeight.bold),
                    ),
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
}
