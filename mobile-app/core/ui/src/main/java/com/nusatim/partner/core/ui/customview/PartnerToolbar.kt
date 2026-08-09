package com.nusatim.partner.core.ui.customview

import android.content.Context
import android.util.AttributeSet
import android.view.LayoutInflater
import android.widget.FrameLayout
import com.nusatim.partner.core.ui.databinding.LayoutPartnerToolbarBinding

class PartnerToolbar @JvmOverloads constructor(
    context: Context,
    attrs: AttributeSet? = null,
    defStyleAttr: Int = 0,
) : FrameLayout(context, attrs, defStyleAttr) {

    private val binding: LayoutPartnerToolbarBinding =
        LayoutPartnerToolbarBinding.inflate(LayoutInflater.from(context), this, true)

    val toolbar get() = binding.toolbar

    fun setTitle(title: String) {
        binding.toolbar.title = title
    }
}
