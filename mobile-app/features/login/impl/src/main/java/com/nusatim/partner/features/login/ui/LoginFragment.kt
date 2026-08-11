package com.nusatim.partner.features.login.ui

import android.util.Log
import android.widget.Toast
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
import com.nusatim.partner.features.login.R
import com.nusatim.partner.features.login.databinding.FragmentLoginBinding
import com.nusatim.partner.features.login.ui.state.LoginEffect
import com.nusatim.partner.features.login.ui.state.LoginIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch

@AndroidEntryPoint
class LoginFragment : BaseFragment<FragmentLoginBinding>() {

    private val viewModel: LoginViewModel by viewModels()

    override fun onInitViews() {
        binding.etEmail.addTextChangedListener {
            viewModel.processIntent(LoginIntent.EmailChanged(it.toString()))
        }
        binding.etPassword.addTextChangedListener {
            viewModel.processIntent(LoginIntent.PasswordChanged(it.toString()))
        }
        binding.btnLogin.setOnClickListener {
            viewModel.processIntent(LoginIntent.Submit)
        }
        binding.tvForgotPassword.setOnClickListener {
            viewModel.processIntent(LoginIntent.NavigateToForgotPassword)
        }
        binding.tvRegisterLink.setOnClickListener {
            viewModel.processIntent(LoginIntent.NavigateToRegister)
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewModel.effect.collect { effect ->
                Log.d("LoginFragment", "Effect received: $effect")
                when (effect) {
                    is LoginEffect.NavigateToHome -> {
                        Log.d("LoginFragment", "Navigating to Home")
                        val navOptions = androidx.navigation.NavOptions.Builder()
                            .setPopUpTo(findNavController().currentDestination?.id ?: -1, true)
                            .build()
                        findNavController().navigate("partner://home".toUri(), navOptions)
                    }
                    is LoginEffect.NavigateToApprovalStatus -> {
                        val navOptions = androidx.navigation.NavOptions.Builder()
                            .setPopUpTo(findNavController().currentDestination?.id ?: -1, true)
                            .build()
                        findNavController().navigate("partner://approval-status".toUri(), navOptions)
                    }
                    is LoginEffect.NavigateToRegister -> {
                        findNavController().navigate("partner://register".toUri())
                    }
                    is LoginEffect.NavigateToForgotPassword -> {
                        findNavController().navigate("partner://forgot-password".toUri())
                    }
                }
            }
        }

        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collectLatest { state ->
                    Log.d("LoginFragment", "State updated: isLoading=${state.isLoading}, error=${state.error}")

                    val isInputValid = state.email.isNotBlank() && state.password.isNotBlank()
                    binding.btnLogin.isEnabled = !state.isLoading && isInputValid

                    // Efek visual untuk tombol disabled
                    binding.btnLogin.alpha = if (binding.btnLogin.isEnabled) 1.0f else 0.5f

                    binding.btnLogin.text = if (state.isLoading) {
                        getString(R.string.login_btn_loading)
                    } else {
                        getString(R.string.login_sign_in)
                    }

                    state.error?.let {
                        showSnackbar(it, SnackbarType.ERROR)
                        viewModel.processIntent(LoginIntent.DismissError)
                    } ?: run {
                        state.errorResId?.let { resId ->
                            showSnackbar(getString(resId), SnackbarType.ERROR)
                            viewModel.processIntent(LoginIntent.DismissError)
                        } ?: run {
                            binding.tilPassword.error = null
                        }
                    }
                }
            }
        }
    }
}
