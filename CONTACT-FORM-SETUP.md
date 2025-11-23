# Contact Form 7 Setup untuk Tempone Theme

## Instalasi Plugin

1. Install **Contact Form 7** dari WordPress admin
2. Activate plugin

## Contact Form Setup

### 1. Buat Form Baru

Di WordPress admin, buka **Contact → Contact Forms** → Add New

### 2. Form Template

Gunakan kode berikut untuk Contact Form 7 (sesuai dengan UI design):

```
<p>
    <label>Name
        [text* your-name placeholder "Masukkan Nama Lengkap"]
    </label>
</p>

<p>
    <label>Property
        [text your-property placeholder "Masukkan Nama Properti"]
    </label>
</p>

<p>
    <label>Email
        [email* your-email placeholder "Masukkan Email"]
    </label>
</p>

<p>
    <label>Phone Number
        [tel* your-phone placeholder "Masukkan Nomor Telepon"]
    </label>
</p>

<p>
    <label>Dimana Anda Mengetahui Diketux?
        [select your-source "Pilih salah satu" "Google Search" "Social Media" "Teman/Keluarga" "Iklan" "Lainnya"]
    </label>
</p>

<p>
    [submit "Submit"]
</p>
```

### 3. Email Settings

**Tab: Mail**

**To:** `[your-email-here]` (email tujuan)

**From:** `[your-email] <wordpress@yourdomain.com>`

**Subject:** `[your-subject] "[your-name] - New Contact Form Submission"`

**Message Body:**
```
From: [your-name] <[your-email]>
Phone: [your-phone]
Property: [your-property]
Source: [your-source]

Message Body:
[your-message]

--
This e-mail was sent from a contact form on [_site_title] ([_site_url])
```

### 4. Buat Contact Page

1. **Pages → Add New**
2. Title: "Book a Demo" atau "Kontak"
   - Title ini akan muncul kecil di atas sebagai H1 (uppercase, font kecil)
3. **Template:** Pilih "Contact Page" dari dropdown Page Attributes
4. **Content:** Masukkan shortcode Contact Form 7:
   ```
   [contact-form-7 id="123" title="Contact Form"]
   ```
   (ID akan otomatis muncul setelah save form di step 2)
5. **Publish**

## Kustomisasi ACF Options

### Contact Page Content (`ane_page_contact`)

Setup ACF Options group **WAJIB** untuk contact page header:

**Group Name:** `ane_page_contact`

**Sub Fields:**
1. **Field Name:** `ane_title` (Text)
   - **Label:** Tagline
   - **Description:** Tagline besar di bawah judul page
   - **Example:** "Kami siap membantu Anda!"
   - **Styling:** Font besar (1.75rem mobile, 2.5rem desktop), bold

2. **Field Name:** `ane_description` (Textarea)
   - **Label:** Description
   - **Description:** Deskripsi lengkap di bawah tagline
   - **Example:** "Jika Anda memiliki pertanyaan, ingin demo, atau ingin bergabung menjadi mitra, jangan ragu untuk menghubungi kami."
   - **Styling:** Font normal (1rem), abu-abu, max-width 600px

**Lokasi:** Isi di **Tempone Setup** atau ACF options page yang Anda buat.

### Company Information

Pastikan data berikut sudah diisi di **Tempone Setup → Company Info**:

- **Company Name:** Nama perusahaan
- **Address:** Alamat lengkap kantor
- **Phone:** Nomor telepon
- **Email:** Email support

Data ini akan otomatis muncul di kolom kanan contact page di 3 cards:
- Email Support
- Call Us
- Office Location

## Styling Notes

Theme sudah include styling untuk:

- ✅ Form fields dengan border abu-abu
- ✅ Placeholder text abu-abu muda
- ✅ Focus state dengan border primary color
- ✅ Submit button biru (#38bdf8) dengan hover effect
- ✅ Validation error messages merah
- ✅ Success message hijau
- ✅ Responsive design (mobile & desktop)
- ✅ Contact info cards di kanan dengan background light

## Field Mapping sesuai UI

| UI Label | CF7 Field | Type | Required |
|----------|-----------|------|----------|
| Name | `[text* your-name]` | text | Yes |
| Property | `[text your-property]` | text | No |
| Email | `[email* your-email]` | email | Yes |
| Phone Number | `[tel* your-phone]` | tel | Yes |
| Dimana Anda Mengetahui Diketux? | `[select your-source]` | select | No |

## Testing

1. Visit contact page di frontend
2. Test form submission dengan valid data
3. Test validation dengan kosongkan required fields
4. Cek email untuk menerima submission
5. Test responsive di mobile & desktop

## Troubleshooting

**Form tidak ter-style dengan baik:**
- Pastikan SCSS sudah di-compile: `npx sass scss/tempone.scss css/tempone.css`
- Clear browser cache

**Email tidak terkirim:**
- Check SMTP settings di hosting
- Install plugin WP Mail SMTP
- Test dengan email tester plugin

**Contact info tidak muncul:**
- Pastikan ACF fields sudah diisi di Tempone Setup
- Check functions `tempone_get_address_contact_data()` dan `tempone_get_company_display_name()`
