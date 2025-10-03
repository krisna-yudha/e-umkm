# E-UMKM Map API Documentation

## 🗺️ Map & Leaflet API Endpoints

Base URL: `http://yourdomain.com/api/v1/map`

Semua endpoint map bersifat **public** (tidak memerlukan authentication) kecuali disebutkan sebaliknya.

---

## 📍 **GET** `/locations`
Mendapatkan semua lokasi UMKM untuk ditampilkan di map.

### Query Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `kategori` | string | No | Filter berdasarkan kategori |
| `search` | string | No | Pencarian berdasarkan nama, alamat, atau kategori |

### Response Format (GeoJSON)
```json
{
    "status": "success",
    "message": "Map locations retrieved successfully",
    "data": {
        "type": "FeatureCollection",
        "features": [
            {
                "type": "Feature",
                "geometry": {
                    "type": "Point",
                    "coordinates": [110.3695, -7.7956]
                },
                "properties": {
                    "id": 1,
                    "nama_umkm": "Warung Bu Sari",
                    "deskripsi": "Warung makan tradisional...",
                    "kategori": "Makanan & Minuman",
                    "alamat": "Jl. Malioboro No. 15, Yogyakarta",
                    "no_telepon": "081234567890",
                    "email": "warungbusari@gmail.com",
                    "gambar": "http://domain.com/storage/path/image.jpg",
                    "status": "active"
                }
            }
        ],
        "total": 3
    }
}
```

### cURL Example
```bash
curl -X GET "http://yourdomain.com/api/v1/map/locations?kategori=Makanan%20%26%20Minuman"
```

---

## 🎯 **POST** `/locations/radius`
Mendapatkan lokasi UMKM dalam radius tertentu dari titik koordinat.

### Request Body
```json
{
    "latitude": -7.7956,
    "longitude": 110.3695,
    "radius": 5,
    "kategori": "Makanan & Minuman"
}
```

### Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `latitude` | float | Yes | Latitude titik pusat (-90 to 90) |
| `longitude` | float | Yes | Longitude titik pusat (-180 to 180) |
| `radius` | float | No | Radius pencarian dalam KM (default: 10, max: 100) |
| `kategori` | string | No | Filter berdasarkan kategori |

### Response (GeoJSON dengan distance)
```json
{
    "status": "success",
    "message": "Nearby locations retrieved successfully",
    "data": {
        "type": "FeatureCollection",
        "features": [
            {
                "type": "Feature",
                "geometry": {
                    "type": "Point",
                    "coordinates": [110.3695, -7.7956]
                },
                "properties": {
                    "id": 1,
                    "nama_umkm": "Warung Bu Sari",
                    "distance_km": 1.23,
                    // ... other properties
                }
            }
        ],
        "total": 2,
        "search_params": {
            "latitude": -7.7956,
            "longitude": 110.3695,
            "radius_km": 5
        }
    }
}
```

### cURL Example
```bash
curl -X POST "http://yourdomain.com/api/v1/map/locations/radius" \
  -H "Content-Type: application/json" \
  -d '{
    "latitude": -7.7956,
    "longitude": 110.3695,
    "radius": 5
  }'
```

---

## 📂 **GET** `/categories`
Mendapatkan semua kategori UMKM yang tersedia untuk filter map.

### Response
```json
{
    "status": "success",
    "message": "Categories retrieved successfully",
    "data": [
        "Makanan & Minuman",
        "Fashion & Tekstil",
        "Kerajinan Tangan",
        "Jasa & Teknologi"
    ]
}
```

---

## 📊 **GET** `/statistics`
Mendapatkan statistik dan batas area untuk map.

### Response
```json
{
    "status": "success",
    "message": "Map statistics retrieved successfully",
    "data": {
        "total_locations": 15,
        "by_category": [
            {
                "kategori": "Makanan & Minuman",
                "count": 8
            },
            {
                "kategori": "Fashion & Tekstil", 
                "count": 4
            }
        ],
        "bounds": {
            "north": -6.9175,
            "south": -7.7956,
            "east": 110.4203,
            "west": 107.6191
        }
    }
}
```

---

## 🏪 **GET** `/location/{id}`
Mendapatkan detail lengkap lokasi UMKM termasuk menu.

### URL Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | Yes | ID UMKM |

### Response
```json
{
    "status": "success",
    "message": "Location details retrieved successfully",
    "data": {
        "type": "Feature",
        "geometry": {
            "type": "Point",
            "coordinates": [110.3695, -7.7956]
        },
        "properties": {
            "id": 1,
            "nama_umkm": "Warung Bu Sari",
            "deskripsi": "Warung makan tradisional...",
            "kategori": "Makanan & Minuman",
            "alamat": "Jl. Malioboro No. 15, Yogyakarta",
            "no_telepon": "081234567890",
            "email": "warungbusari@gmail.com",
            "gambar": "http://domain.com/storage/path/image.jpg",
            "website": "https://warungbusari.com",
            "facebook": "warungbusari",
            "instagram": "@warungbusari",
            "whatsapp": "6281234567890",
            "status": "active",
            "owner": {
                "name": "Sari Dewi",
                "email": "sari.dewi@example.com"
            },
            "menus": [
                {
                    "id": 1,
                    "nama_menu": "Gudeg Yogya",
                    "deskripsi": "Gudeg tradisional khas Yogyakarta",
                    "harga": 15000,
                    "gambar": "http://domain.com/storage/menu/gudeg.jpg",
                    "kategori_menu": "Makanan Utama",
                    "is_available": true
                }
            ]
        }
    }
}
```

---

## 🗺️ Implementasi dengan Leaflet.js

### Basic Map Setup
```javascript
// Initialize map
const map = L.map('map').setView([-7.7956, 110.3695], 10);

// Add tile layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Fetch and display all locations
fetch('/api/v1/map/locations')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Add GeoJSON layer
            L.geoJSON(data.data, {
                pointToLayer: function (feature, latlng) {
                    return L.marker(latlng);
                },
                onEachFeature: function (feature, layer) {
                    const props = feature.properties;
                    layer.bindPopup(`
                        <div class="popup-content">
                            <h3>${props.nama_umkm}</h3>
                            <p><strong>Kategori:</strong> ${props.kategori}</p>
                            <p><strong>Alamat:</strong> ${props.alamat}</p>
                            <p><strong>Telepon:</strong> ${props.no_telepon}</p>
                            ${props.gambar ? `<img src="${props.gambar}" style="width:100%; max-width:200px;">` : ''}
                        </div>
                    `);
                }
            }).addTo(map);
        }
    });
```

### Search by Radius
```javascript
// Get user location and search nearby
navigator.geolocation.getCurrentPosition(function(position) {
    const userLat = position.coords.latitude;
    const userLng = position.coords.longitude;
    
    fetch('/api/v1/map/locations/radius', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            latitude: userLat,
            longitude: userLng,
            radius: 5 // 5km radius
        })
    })
    .then(response => response.json())
    .then(data => {
        // Display nearby locations
        console.log(`Found ${data.data.total} locations within 5km`);
    });
});
```

### Category Filter
```javascript
// Load categories for filter
fetch('/api/v1/map/categories')
    .then(response => response.json())
    .then(data => {
        const select = document.getElementById('category-filter');
        data.data.forEach(category => {
            const option = document.createElement('option');
            option.value = category;
            option.text = category;
            select.appendChild(option);
        });
    });

// Filter by category
function filterByCategory(kategori) {
    const url = kategori ? `/api/v1/map/locations?kategori=${encodeURIComponent(kategori)}` : '/api/v1/map/locations';
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Clear existing markers
            map.eachLayer(layer => {
                if (layer instanceof L.GeoJSON) {
                    map.removeLayer(layer);
                }
            });
            
            // Add filtered markers
            L.geoJSON(data.data).addTo(map);
        });
}
```

### Auto-fit Map Bounds
```javascript
// Get map statistics to auto-fit bounds
fetch('/api/v1/map/statistics')
    .then(response => response.json())
    .then(data => {
        const bounds = data.data.bounds;
        map.fitBounds([
            [bounds.south, bounds.west],
            [bounds.north, bounds.east]
        ], { padding: [20, 20] });
    });
```

---

## 🔧 Error Handling

### Common Error Responses
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "latitude": ["The latitude field is required."],
        "longitude": ["The longitude field is required."]
    }
}
```

### HTTP Status Codes
- `200` - Success
- `404` - Location not found
- `422` - Validation error
- `500` - Server error

---

## 📱 Mobile Integration Examples

### React Native
```javascript
import MapView, { Marker } from 'react-native-maps';

const MapScreen = () => {
    const [locations, setLocations] = useState([]);
    
    useEffect(() => {
        fetch('http://yourdomain.com/api/v1/map/locations')
            .then(response => response.json())
            .then(data => {
                setLocations(data.data.features);
            });
    }, []);
    
    return (
        <MapView style={styles.map}>
            {locations.map(location => (
                <Marker
                    key={location.properties.id}
                    coordinate={{
                        latitude: location.geometry.coordinates[1],
                        longitude: location.geometry.coordinates[0]
                    }}
                    title={location.properties.nama_umkm}
                    description={location.properties.alamat}
                />
            ))}
        </MapView>
    );
};
```

### Flutter
```dart
import 'package:flutter_map/flutter_map.dart';

class MapWidget extends StatefulWidget {
  @override
  _MapWidgetState createState() => _MapWidgetState();
}

class _MapWidgetState extends State<MapWidget> {
  List<Marker> markers = [];
  
  @override
  void initState() {
    super.initState();
    loadLocations();
  }
  
  void loadLocations() async {
    final response = await http.get(
      Uri.parse('http://yourdomain.com/api/v1/map/locations')
    );
    
    final data = json.decode(response.body);
    setState(() {
      markers = data['data']['features'].map<Marker>((location) {
        return Marker(
          point: LatLng(
            location['geometry']['coordinates'][1],
            location['geometry']['coordinates'][0]
          ),
          child: Icon(Icons.location_pin),
        );
      }).toList();
    });
  }
  
  @override
  Widget build(BuildContext context) {
    return FlutterMap(
      options: MapOptions(
        initialCenter: LatLng(-7.7956, 110.3695),
        initialZoom: 10,
      ),
      children: [
        TileLayer(
          urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        ),
        MarkerLayer(markers: markers),
      ],
    );
  }
}
```

---

## 🚀 Production Tips

1. **Rate Limiting**: Implementasikan rate limiting untuk endpoint publik
2. **Caching**: Cache response untuk `/categories` dan `/statistics`
3. **Image Optimization**: Compress dan resize gambar UMKM
4. **CDN**: Gunakan CDN untuk serve gambar dan tile maps
5. **Monitoring**: Monitor usage dan performance endpoint map

API Map ini siap digunakan untuk integrasi dengan berbagai platform dan framework mapping! 🗺️