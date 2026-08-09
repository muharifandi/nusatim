package com.nusatim.partner.features.profile.ui.edit

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.profile.databinding.FragmentEditProfileBinding
import com.nusatim.partner.features.profile.ui.main.ProfileViewModel
import com.nusatim.partner.features.profile.ui.main.state.ProfileEffect
import com.nusatim.partner.features.profile.ui.main.state.ProfileIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class EditProfileFragment : BaseFragment<FragmentEditProfileBinding>() {

    private val viewModel: ProfileViewModel by viewModels()

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.apply {
            title = getString(com.nusatim.partner.features.profile.R.string.profile_edit_title)
            setNavigationOnClickListener {
                findNavController().navigateUp()
            }
        }

        binding.btnSave.setOnClickListener {
            val data = mapOf(
                "name" to binding.etName.text.toString(),
                "email" to binding.etEmail.text.toString(),
                "bank_name" to binding.etBankName.text.toString(),
                "bank_account_number" to binding.etBankAccountNumber.text.toString(),
                "bank_account_holder" to binding.etBankAccountHolder.text.toString(),
                "email_notifications_enabled" to binding.swEmailNotif.isChecked
            )
            viewModel.processIntent(ProfileIntent.UpdateProfile(data))
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.state.collect { state ->
                        state.partner?.let { partner ->
                            if (binding.etName.text.isNullOrEmpty()) {
                                binding.etName.setText(partner.name)
                                binding.etEmail.setText(partner.email)
                                binding.etBankName.setText(partner.bankName)
                                binding.etBankAccountNumber.setText(partner.bankAccountNumber)
                                binding.etBankAccountHolder.setText(partner.bankAccountHolder)
                                binding.swEmailNotif.isChecked = partner.emailNotificationsEnabled
                            }
                        }
                        
                        binding.btnSave.isEnabled = !state.isLoading
                    }
                }

                launch {
                    viewModel.effect.collect { effect ->
                        when (effect) {
                            is ProfileEffect.SuccessUpdate -> {
                                findNavController().navigateUp()
                            }
                            is ProfileEffect.ShowToast -> {
                                // Show error toast
                            }
                            else -> {}
                        }
                    }
                }
            }
        }
    }
}
