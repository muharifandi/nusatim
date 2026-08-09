package com.nusatim.partner.core.architecture.base

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.viewbinding.ViewBinding
import java.lang.reflect.ParameterizedType

/**
 * Base Fragment dengan Auto-Binding menggunakan Reflection.
 * Tidak perlu lagi menulis delegate manual.
 */
abstract class BaseFragment<VB : ViewBinding> : Fragment() {

    private var _binding: VB? = null
    val binding get() = _binding!!

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val type = javaClass.genericSuperclass as ParameterizedType
        val clazz = type.actualTypeArguments[0] as Class<VB>
        val method = clazz.getMethod("inflate", LayoutInflater::class.java, ViewGroup::class.java, Boolean::class.javaPrimitiveType)
        
        @Suppress("UNCHECKED_CAST")
        _binding = method.invoke(null, inflater, container, false) as VB
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        onInitViews()
        onInitObservers()
    }

    abstract fun onInitViews()
    open fun onInitObservers() {}

    fun showLoading(show: Boolean) {
        (activity as? BaseActivity<*>)?.showLoading(show)
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
