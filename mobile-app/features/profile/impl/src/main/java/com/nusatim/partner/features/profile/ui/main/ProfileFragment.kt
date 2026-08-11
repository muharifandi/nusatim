package com.nusatim.partner.features.profile.ui.main

import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.NavOptions
import androidx.navigation.fragment.findNavController
import coil.load
import coil.transform.CircleCropTransformation
import com.google.android.material.dialog.MaterialAlertDialogBuilder
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.profile.R
import com.nusatim.partner.features.profile.databinding.FragmentProfileBinding
import com.nusatim.partner.features.profile.ui.main.state.ProfileEffect
import com.nusatim.partner.features.profile.ui.main.state.ProfileIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class ProfileFragment : BaseFragment<FragmentProfileBinding>() {

    private val viewModel: ProfileViewModel by viewModels()

    override fun onInitViews() {
        binding.menuLogout.root.setOnClickListener {
            showLogoutDialog()
        }

        binding.menuDeleteAccount.root.setOnClickListener {
            showDeleteAccountDialog()
        }

        binding.menuEditProfile.root.setOnClickListener {
            findNavController().navigate("partner://profile/edit".toUri())
        }

        binding.menuKyc.root.setOnClickListener {
            findNavController().navigate("partner://profile/kyc".toUri())
        }

        binding.menuChangePassword.root.setOnClickListener {
            findNavController().navigate("partner://profile/password".toUri())
        }

        binding.menuMarketing.root.setOnClickListener {
            findNavController().navigate("partner://marketing".toUri())
        }

        binding.menuSupport.root.setOnClickListener {
            findNavController().navigate("partner://support".toUri())
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.state.collect { state ->
                        state.partner?.let { partner ->
                            binding.tvName.text = partner.name
                            binding.tvEmail.text = partner.email
                            binding.ivProfilePhoto.load(partner.profilePhotoUrl) {
                                transformations(CircleCropTransformation())
                                placeholder(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
                                error(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
                            }

                            binding.tvStatusText.text = partner.status.replaceFirstChar { it.uppercase() }
                        }
                    }
                }

                launch {
                    viewModel.effect.collect { effect ->
                        when (effect) {
                            is ProfileEffect.LogoutSuccess,
                            is ProfileEffect.DeleteAccountSuccess -> {
                                val navOptions = NavOptions.Builder()
                                    .setPopUpTo(findNavController().graph.id, true)
                                    .build()
                                findNavController().navigate("partner://login".toUri(), navOptions)
                            }
                            else -> {}
                        }
                    }
                }
            }
        }
    }

    private fun showLogoutDialog() {
        MaterialAlertDialogBuilder(requireContext())
            .setTitle("Keluar")
            .setMessage("Yakin ingin keluar?")
            .setPositiveButton("Ya") { _, _ ->
                viewModel.processIntent(ProfileIntent.Logout)
            }
            .setNegativeButton("Batal", null)
            .show()
    }

    private fun showDeleteAccountDialog() {
        MaterialAlertDialogBuilder(requireContext())
            .setTitle(getString(R.string.profile_delete_account_dialog_title))
            .setMessage(getString(R.string.profile_delete_account_dialog_message))
            .setPositiveButton(getString(R.string.profile_delete_account_btn_confirm)) { _, _ ->
                viewModel.processIntent(ProfileIntent.DeleteAccount)
            }
            .setNegativeButton(getString(com.nusatim.partner.core.ui.R.string.cancel), null)
            .show()
    }
}
