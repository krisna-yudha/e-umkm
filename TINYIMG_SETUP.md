# TinyIMG Image Compression Setup Guide

## Overview
The application now supports automatic image compression using TinyIMG (Tinify) API when users upload profile photos.

## Installation Status

### Backend (PHP)
✅ **Tinify PHP Library** has been added via Composer
- Package: `tinify/tinify`
- Auto-compression on image upload
- Fallback compression method available

### Frontend (Vue.js)
✅ **Tinify JS Package** installed via npm
- Real-time image compression preview
- Modal-based compression interface

## Configuration

### 1. Get TinyIMG API Key
- Visit: https://tinify.com/developers
- Sign up or log in to your account
- Create an API key
- You get 500 free compressions per month

### 2. Add API Key to Environment
Add the following to your `.env` file:

```env
TINIFY_API_KEY=your_api_key_here
```

Replace `your_api_key_here` with your actual API key from TinyIMG.

### 3. Restart the Application
After updating `.env`, restart your Laravel application:
```bash
php artisan cache:clear
php artisan config:cache
```

## How It Works

### Profile Photo Upload Flow
1. User clicks "📸 Pilih Foto" button in Profile Edit page
2. User selects an image file
3. **SimpleImageCompressor** modal opens automatically
4. User sees original file size
5. User clicks "🗜️ Kompres & Simpan" button
6. Image is sent to backend `/api/compress-image` endpoint
7. Backend compresses using TinyIMG API (or fallback method)
8. Compressed image returned as base64
9. Preview updates with compressed image
10. User submits form with compressed image
11. Profile photo is updated in database

### Compression Benefits
- **Original Size**: Before compression
- **Compressed Size**: After compression
- **Savings**: Percentage reduction shown to user
- **Quality**: Maintained at professional level (85% JPEG quality in fallback, optimal via TinyIMG)

## Features

### TinyIMG API Compression
- Uses TinyIMG's AI-powered compression
- Best compression ratio
- Requires API key and internet connection
- Requires active subscription

### Fallback Compression Method
- Automatic JPEG conversion with 85% quality
- Works without API key
- Local processing, no external calls
- Reasonable compression ratio

### Error Handling
- Graceful fallback if API key missing
- Detailed error messages to user
- Logging of all compression operations
- Support for multiple image formats: JPEG, PNG, GIF, WebP

## MIME Types Supported
- image/jpeg
- image/png
- image/gif
- image/webp
- Maximum file size: 5MB before compression

## Logging
All compression operations are logged to:
```
storage/logs/laravel.log
```

Check logs for:
- Compression ratios achieved
- API errors
- Fallback method usage
- Failed compressions

## Testing

### Manual Testing
1. Go to Profile Edit page (`/profile`)
2. Click "📸 Pilih Foto" button
3. Select a large image (>1MB recommended)
4. Watch the compression progress
5. Verify compressed size is smaller
6. Submit the form
7. Verify profile photo is updated

### Sample API Call (for debugging)
```bash
curl -X POST http://your-app.local/api/compress-image \
  -H "Authorization: Bearer your_token" \
  -F "image=@/path/to/image.jpg"
```

## Troubleshooting

### Issue: "API key not configured"
**Solution**: Add `TINIFY_API_KEY` to `.env` file

### Issue: "Compression failed with status 401"
**Solution**: Verify API key is correct and active in TinyIMG account

### Issue: "Compression failed with status 429"
**Solution**: Free tier limit reached (500/month). Upgrade or wait for next month

### Issue: Fallback compression is used instead of TinyIMG
**Solution**: Check if API key is set correctly in `.env`

### Issue: Image upload still fails after compression
**Solution**: 
- Check file size is under 5MB
- Verify image format is supported
- Check Laravel storage permissions
- Review `storage/logs/laravel.log`

## API Endpoint

### POST `/api/compress-image`
Compress and return image as base64 encoded JPEG

**Request:**
- Method: POST
- Auth: Required (Sanctum)
- Content-Type: multipart/form-data
- Field: `image` (File - required)

**Response (Success - 200):**
```json
{
  "success": true,
  "compressedImage": "data:image/jpeg;base64,...",
  "originalSize": 2048576,
  "compressedSize": 614400,
  "message": "Gambar berhasil dikompres dengan TinyIMG"
}
```

**Response (Error - 400/500):**
```json
{
  "success": false,
  "message": "Error message here"
}
```

## File Locations

| File | Purpose |
|------|---------|
| `app/Http/Controllers/ImageCompressionController.php` | Backend compression logic |
| `resources/js/Components/SimpleImageCompressor.vue` | Compression modal component |
| `resources/js/Pages/Profile/Partials/UpdateProfileInformationForm.vue` | Profile form with compression integration |
| `routes/web.php` | API route definition |

## Future Enhancements

- [ ] Batch compression for multiple images
- [ ] Image resizing options in UI
- [ ] Compression analytics dashboard
- [ ] UMKM image compression integration
- [ ] Scheduled compression tasks
- [ ] CDN integration for optimized delivery

## Cost Considerations

**TinyIMG Free Plan:**
- 500 compressions/month
- No credit card required
- Perfect for small to medium sites

**Premium Plans:**
- Unlimited compressions
- Starting from $4.99/month
- Visit: https://tinify.com/developers/pricing

