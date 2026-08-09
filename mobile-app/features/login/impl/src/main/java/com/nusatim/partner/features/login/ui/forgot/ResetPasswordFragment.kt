package com.nusatim.partner.features.login.ui.forgot

import androidx.core.net.toUri
import android.os.Bundle
import android.widget.Toast
import androidx.core.widget.addTextChangedListener
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.ui.util.SnackbarType
import com.nusatim.partner.core.ui.util.showSnackbar
import com.nusatim.partner.features.login.databinding.FragmentResetPasswordBinding
import com.nusatim.partner.features.login.ui.forgot.state.ResetPasswordEffect
import com.nusatim.partner.features.login.ui.forgot.state.ResetPasswordIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch

@AndroidEntryPoint
class ResetPasswordFragment : BaseFragment<FragmentResetPasswordBinding>() {

    private val viewModel: ResetPasswordViewModel by viewModels()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        val email = arguments?.getString("email") ?: ""
        viewModel.processIntent(ResetPasswordIntent.EmailChanged(email))
    }

    override fun onInitViews() {
        binding.etCode.addTextChangedListener {
            viewModel.processIntent(ResetPasswordIntent.CodeChanged(it.toString()))
        }
        binding.etPassword.addTextChangedListener {
            viewModel.processIntent(ResetPasswordIntent.PasswordChanged(it.toString()))
        }
        binding.etPasswordConfirmation.addTextChangedListener {
            viewModel.processIntent(ResetPasswordIntent.PasswordConfirmationChanged(it.toString()))
        }
        binding.btnSubmit.setOnClickListener {
            viewModel.processIntent(ResetPasswordIntent.Submit)
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewModel.effect.collect { effect ->
                when (effect) {
                    is ResetPasswordEffect.NavigateToLogin -> {
                        Toast.makeText(requireContext(), "Password berhasil direset, silakan login kembali.", Toast.LENGTH_LONG).show()
                        findNavController().navigate("partner://login".toUri()) {
                            popUpTo("partner://login".toUri()) { inclusive = true }
                        }
                    }
                }
            }
        }

        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collectLatest { state ->
                    binding.btnSubmit.isEnabled = !state.isLoading && 
                            state.code.isNotBlank() && 
                            state.password.isNotBlank() && 
                            state.passwordConfirmation.isNotBlank()
                    
                    binding.btnSubmit.text = if (state.isLoading) "Memproses..." else "Reset Password"
                    
                    state.error?.let {
                        showSnackbar(it, SnackbarType.ERROR)
                        viewModel.processIntent(ResetPasswordIntent.DismissError)
                    }
                }
            }
        }
    }
}
