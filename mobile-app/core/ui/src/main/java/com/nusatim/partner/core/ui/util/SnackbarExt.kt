package com.nusatim.partner.core.ui.util

import android.graphics.Color
import android.view.Gravity
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.FrameLayout
import android.widget.ImageView
import android.widget.TextView
import androidx.core.content.ContextCompat
import androidx.fragment.app.Fragment
import com.google.android.material.snackbar.Snackbar
import com.nusatim.partner.core.ui.R

enum class SnackbarType {
    SUCCESS, ERROR, INFO, WARNING
}

/**
 * Modern Custom Snackbar dengan desain "Floating Pill" di bagian atas.
 * Memperbaiki masalah padding asimetris dan tampilan klasik.
 */
fun Fragment.showSnackbar(
    message: String,
    type: SnackbarType = SnackbarType.INFO,
    anchorView: View? = null
) {
    val rootView = anchorView ?: view ?: return
    
    // Gunakan durasi sedikit lebih lama agar terbaca (modern style)
    val snackbar = Snackbar.make(rootView, "", 4000)
    
    val snackbarLayout = snackbar.view as ViewGroup
    
    // 1. Bersihkan styling default
    snackbarLayout.setBackgroundColor(Color.TRANSPARENT)
    snackbarLayout.setPadding(0, 0, 0, 0)
    
    // Sembunyikan view bawaan snackbar
    snackbarLayout.findViewById<View>(com.google.android.material.R.id.snackbar_text)?.visibility = View.GONE
    snackbarLayout.findViewById<View>(com.google.android.material.R.id.snackbar_action)?.visibility = View.GONE

    // 2. Inflate Custom View
    val inflater = LayoutInflater.from(requireContext())
    val customView = inflater.inflate(R.layout.layout_custom_snackbar, snackbarLayout, false)

    val tvTitle = customView.findViewById<TextView>(R.id.tv_title)
    val tvMessage = customView.findViewById<TextView>(R.id.tv_message)
    val ivIcon = customView.findViewById<ImageView>(R.id.iv_icon)

    tvMessage.text = message

    // 3. Konfigurasi Visual Modern
    val (title, iconRes, colorRes, bgIconRes) = when (type) {
        SnackbarType.SUCCESS -> Quad(
            "Berhasil",
            android.R.drawable.ic_dialog_info, // Ganti dengan ic_check jika ada
            R.color.status_success,
            R.drawable.bg_circle_green_light
        )
        SnackbarType.ERROR -> Quad(
            "Gagal",
            android.R.drawable.ic_delete,
            R.color.partner_error,
            R.drawable.bg_circle_orange_light // Gunakan light red jika ada
        )
        SnackbarType.WARNING -> Quad(
            "Peringatan",
            android.R.drawable.ic_dialog_alert,
            R.color.status_warning,
            R.drawable.bg_circle_orange_light
        )
        SnackbarType.INFO -> Quad(
            "Informasi",
            android.R.drawable.ic_dialog_info,
            R.color.status_info,
            R.drawable.bg_circle_orange_light // Gunakan light blue jika ada
        )
    }

    tvTitle.text = title
    tvTitle.setTextColor(ContextCompat.getColor(requireContext(), colorRes))
    ivIcon.setImageResource(iconRes)
    ivIcon.setColorFilter(ContextCompat.getColor(requireContext(), colorRes))
    ivIcon.setBackgroundResource(bgIconRes)
    
    // 4. Pasang ke Snackbar
    snackbarLayout.addView(customView)

    // 5. Perbaikan Posisi & Padding (Menghilangkan "Lari ke Kanan")
    val params = snackbarLayout.layoutParams
    if (params is FrameLayout.LayoutParams) {
        // Force MATCH_PARENT agar tidak lari ke kanan di screen lebar
        params.width = FrameLayout.LayoutParams.MATCH_PARENT
        params.gravity = Gravity.TOP
        
        val density = requireContext().resources.displayMetrics.density
        val marginHorizontal = (16 * density).toInt()
        val marginTop = (56 * density).toInt() // Sedikit di bawah status bar
        
        // Pastikan margin kiri dan kanan SAMA (simetris)
        params.setMargins(marginHorizontal, marginTop, marginHorizontal, 0)
        snackbarLayout.layoutParams = params
    }

    snackbar.show()
}

// Helper class sederhana
private data class Quad<A, B, C, D>(val first: A, val second: B, val third: C, val fourth: D)
