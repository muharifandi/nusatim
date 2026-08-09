package com.nusatim.partner.core.architecture.base

import android.os.Bundle
import android.view.LayoutInflater
import androidx.appcompat.app.AppCompatActivity
import androidx.viewbinding.ViewBinding
import java.lang.reflect.ParameterizedType

/**
 * Base Activity dengan Auto-Binding menggunakan Reflection.
 * Developer tidak perlu lagi menulis delegate manual di subclass.
 */
abstract class BaseActivity<VB : ViewBinding> : AppCompatActivity() {

    private var _binding: VB? = null
    val binding get() = _binding!!

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        // Reflection untuk mencari class VB dan memanggil method inflate
        val type = javaClass.genericSuperclass as ParameterizedType
        val clazz = type.actualTypeArguments[0] as Class<VB>
        val method = clazz.getMethod("inflate", LayoutInflater::class.java)
        
        @Suppress("UNCHECKED_CAST")
        _binding = method.invoke(null, layoutInflater) as VB
        
        setContentView(binding.root)
        
        onInitViews()
        onInitObservers()
    }

    abstract fun onInitViews()
    open fun onInitObservers() {}

    fun showLoading(show: Boolean) {
        val loadingView = findViewById<android.view.View>(com.nusatim.partner.core.ui.R.id.container_loading)
        loadingView?.visibility = if (show) android.view.View.VISIBLE else android.view.View.GONE
    }

    override fun onDestroy() {
        super.onDestroy()
        _binding = null
    }
}
