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
    val (titleRes, iconRes, colorRes, bgIconRes) = when (type) {
        SnackbarType.SUCCESS -> Quad(
            R.string.snackbar_success,
            android.R.drawable.ic_dialog_info,
            R.color.status_success,
            R.drawable.bg_circle_green_light
        )
        SnackbarType.ERROR -> Quad(
            R.string.snackbar_error,
            android.R.drawable.ic_delete,
            R.color.partner_error,
            R.drawable.bg_circle_orange_light
        )
        SnackbarType.WARNING -> Quad(
            R.string.snackbar_warning,
            android.R.drawable.ic_dialog_alert,
            R.color.status_warning,
            R.drawable.bg_circle_orange_light
        )
        SnackbarType.INFO -> Quad(
            R.string.snackbar_info,
            android.R.drawable.ic_dialog_info,
            R.color.status_info,
            R.drawable.bg_circle_orange_light
        )
    }

    tvTitle.text = getString(titleRes)
    tvTitle.setTextColor(ContextCompat.getColor(requireContext(), colorRes))
    ivIcon.setImageResource(iconRes)
    ivIcon.setColorFilter(ContextCompat.getColor(requireContext(), colorRes))
    ivIcon.setBackgroundResource(bgIconRes)

    // 4. Pasang ke Snackbar
    snackbarLayout.addView(customView)

    // 5. Perbaikan Posisi & Padding
    val params = snackbarLayout.layoutParams
    if (params is FrameLayout.LayoutParams) {
        params.width = FrameLayout.LayoutParams.MATCH_PARENT
        params.gravity = Gravity.TOP

        val marginHorizontal = requireContext().resources.getDimensionPixelSize(R.dimen.partner_space_m)
        val marginTop = requireContext().resources.getDimensionPixelSize(R.dimen.partner_space_xl) * 2

        params.setMargins(marginHorizontal, marginTop, marginHorizontal, 0)
        snackbarLayout.layoutParams = params
    }

    snackbar.show()
}

// Helper class sederhana
private data class Quad<A, B, C, D>(val first: A, val second: B, val third: C, val fourth: D)
