package com.nusatim.partner.features.profile.ui.kyc

import android.net.Uri
import androidx.activity.result.contract.ActivityResultContracts
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import coil.load
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.profile.databinding.FragmentKycDocumentsBinding
import com.nusatim.partner.features.profile.ui.main.ProfileViewModel
import com.nusatim.partner.features.profile.ui.main.state.ProfileEffect
import com.nusatim.partner.features.profile.ui.main.state.ProfileIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch
import java.io.File
import java.io.FileOutputStream

@AndroidEntryPoint
class KycDocumentsFragment : BaseFragment<FragmentKycDocumentsBinding>() {

    private val viewModel: ProfileViewModel by viewModels()
    private var currentUploadType: String? = null

    private val pickImage = registerForActivityResult(ActivityResultContracts.GetContent()) { uri: Uri? ->
        uri?.let { handleImagePicked(it) }
    }

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.apply {
            title = getString(com.nusatim.partner.features.profile.R.string.profile_kyc_title)
            setNavigationOnClickListener {
                findNavController().navigateUp()
            }
        }

        binding.btnUpdatePhoto.setOnClickListener {
            currentUploadType = "photo"
            pickImage.launch("image/*")
        }

        binding.btnUpdateKtp.setOnClickListener {
            currentUploadType = "ktp"
            pickImage.launch("image/*")
        }

        binding.btnUpdateNpwp.setOnClickListener {
            currentUploadType = "npwp"
            pickImage.launch("image/*")
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.state.collect { state ->
                        state.partner?.let { partner ->
                            binding.ivPhotoPreview.load(partner.profilePhotoUrl) {
                                placeholder(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
                                error(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
                            }
                            binding.ivKtpPreview.load(partner.ktpUrl) {
                                placeholder(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
                                error(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
                            }
                            binding.ivNpwpPreview.load(partner.npwpUrl) {
                                placeholder(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
                                error(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
                            }
                        }
                    }
                }

                launch {
                    viewModel.effect.collect { effect ->
                        when (effect) {
                            is ProfileEffect.SuccessUpdate -> {
                                // Handled by state observer for UI updates
                                com.google.android.material.snackbar.Snackbar.make(
                                    binding.root,
                                    "Dokumen berhasil diperbarui",
                                    com.google.android.material.snackbar.Snackbar.LENGTH_SHORT
                                ).show()
                            }
                            is ProfileEffect.ShowToast -> {
                                // Show error
                            }
                            else -> {}
                        }
                    }
                }
            }
        }
    }

    private fun handleImagePicked(uri: Uri) {
        val file = uriToFile(uri)
        when (currentUploadType) {
            "photo" -> viewModel.processIntent(ProfileIntent.UpdatePhoto(file))
            "ktp" -> viewModel.processIntent(ProfileIntent.UpdateKtp(file))
            "npwp" -> viewModel.processIntent(ProfileIntent.UpdateNpwp(file))
        }
    }

    private fun uriToFile(uri: Uri): File {
        val inputStream = requireContext().contentResolver.openInputStream(uri)
        val file = File(requireContext().cacheDir, "temp_image_${System.currentTimeMillis()}.jpg")
        val outputStream = FileOutputStream(file)
        inputStream?.use { input ->
            outputStream.use { output ->
                input.copyTo(output)
            }
        }
        return file
    }
}
