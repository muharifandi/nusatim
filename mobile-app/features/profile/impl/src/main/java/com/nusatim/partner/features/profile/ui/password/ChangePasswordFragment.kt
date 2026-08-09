package com.nusatim.partner.features.profile.ui.password

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.profile.databinding.FragmentChangePasswordBinding
import com.nusatim.partner.features.profile.ui.main.ProfileViewModel
import com.nusatim.partner.features.profile.ui.main.state.ProfileEffect
import com.nusatim.partner.features.profile.ui.main.state.ProfileIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class ChangePasswordFragment : BaseFragment<FragmentChangePasswordBinding>() {

    private val viewModel: ProfileViewModel by viewModels()

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.apply {
            title = getString(com.nusatim.partner.features.profile.R.string.profile_password_title)
            setNavigationOnClickListener {
                findNavController().navigateUp()
            }
        }

        binding.btnSave.setOnClickListener {
            val current = binding.etCurrentPassword.text.toString()
            val new = binding.etNewPassword.text.toString()
            val confirm = binding.etConfirmPassword.text.toString()
            
            if (new != confirm) {
                // Show mismatch error
                return@setOnClickListener
            }
            
            viewModel.processIntent(ProfileIntent.UpdatePassword(current, new, confirm))
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.effect.collect { effect ->
                        when (effect) {
                            is ProfileEffect.SuccessUpdate -> {
                                findNavController().navigateUp()
                            }
                            is ProfileEffect.ShowToast -> {
                                // Show error
                            }
                            else -> {}
                        }
                    }
                }
                
                launch {
                    viewModel.state.collect { state ->
                        binding.btnSave.isEnabled = !state.isLoading
                    }
                }
            }
        }
    }
}
