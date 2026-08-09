package com.nusatim.partner.features.login.ui.forgot

import androidx.core.net.toUri
import androidx.core.widget.addTextChangedListener
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.ui.util.SnackbarType
import com.nusatim.partner.core.ui.util.showSnackbar
import com.nusatim.partner.features.login.databinding.FragmentForgotPasswordBinding
import com.nusatim.partner.features.login.ui.forgot.state.ForgotPasswordEffect
import com.nusatim.partner.features.login.ui.forgot.state.ForgotPasswordIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch

@AndroidEntryPoint
class ForgotPasswordFragment : BaseFragment<FragmentForgotPasswordBinding>() {

    private val viewModel: ForgotPasswordViewModel by viewModels()

    override fun onInitViews() {
        binding.ivBack.setOnClickListener {
            findNavController().navigateUp()
        }
        binding.etEmail.addTextChangedListener {
            viewModel.processIntent(ForgotPasswordIntent.EmailChanged(it.toString()))
        }
        binding.btnSubmit.setOnClickListener {
            viewModel.processIntent(ForgotPasswordIntent.Submit)
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewModel.effect.collect { effect ->
                when (effect) {
                    is ForgotPasswordEffect.NavigateToResetPassword -> {
                        findNavController().navigate("partner://reset-password?email=${effect.email}".toUri())
                    }
                }
            }
        }

        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collectLatest { state ->
                    binding.btnSubmit.isEnabled = !state.isLoading && state.email.isNotBlank()
                    binding.btnSubmit.text = if (state.isLoading) "Memproses..." else "Kirim Kode"
                    
                    state.error?.let {
                        showSnackbar(it, SnackbarType.ERROR)
                        viewModel.processIntent(ForgotPasswordIntent.DismissError)
                    }
                }
            }
        }
    }
}
