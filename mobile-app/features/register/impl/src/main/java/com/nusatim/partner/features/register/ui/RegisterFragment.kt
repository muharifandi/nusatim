package com.nusatim.partner.features.register.ui

import android.view.View
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.core.widget.addTextChangedListener
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.ui.util.SnackbarType
import com.nusatim.partner.core.ui.util.showSnackbar
import com.nusatim.partner.features.register.databinding.FragmentRegisterBinding
import com.nusatim.partner.features.register.ui.state.RegisterEffect
import com.nusatim.partner.features.register.ui.state.RegisterIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import java.io.File
import java.io.FileOutputStream

@AndroidEntryPoint
class RegisterFragment : BaseFragment<FragmentRegisterBinding>() {

    private val viewModel: RegisterViewModel by viewModels()

    private var currentPickingType: PickingType? = null

    private enum class PickingType { PROFILE, KTP, NPWP }

    private val imagePicker = registerForActivityResult(ActivityResultContracts.GetContent()) { uri ->
        uri?.let {
            val file = uriToFile(it)
            when (currentPickingType) {
                PickingType.PROFILE -> viewModel.processIntent(RegisterIntent.ProfilePhotoPicked(file))
                PickingType.KTP -> viewModel.processIntent(RegisterIntent.KtpPicked(file))
                PickingType.NPWP -> viewModel.processIntent(RegisterIntent.NpwpPicked(file))
                null -> {}
            }
        }
    }

    override fun onInitViews() {
        setupStep1Listeners()
        setupStep2Listeners()
        setupStep3Listeners()
        setupStep4Listeners()

        binding.btnNext.setOnClickListener {
            val currentState = viewModel.state.value
            if (currentState.currentStep < currentState.totalSteps) {
                viewModel.processIntent(RegisterIntent.NextStep)
            } else {
                viewModel.processIntent(RegisterIntent.Submit)
            }
        }

        binding.btnBack.setOnClickListener {
            viewModel.processIntent(RegisterIntent.PreviousStep)
        }
    }

    private fun setupStep1Listeners() {
        binding.step1.etName.addTextChangedListener { viewModel.processIntent(RegisterIntent.NameChanged(it.toString())) }
        binding.step1.etEmail.addTextChangedListener { viewModel.processIntent(RegisterIntent.EmailChanged(it.toString())) }
        binding.step1.etPassword.addTextChangedListener { viewModel.processIntent(RegisterIntent.PasswordChanged(it.toString())) }
        binding.step1.etPasswordConfirm.addTextChangedListener { viewModel.processIntent(RegisterIntent.PasswordConfirmationChanged(it.toString())) }
    }

    private fun setupStep2Listeners() {
        binding.step2.cardPhotoProfile.root.setOnClickListener {
            currentPickingType = PickingType.PROFILE
            imagePicker.launch("image/*")
        }
        binding.step2.cardPhotoKtp.root.setOnClickListener {
            currentPickingType = PickingType.KTP
            imagePicker.launch("image/*")
        }
        binding.step2.cardPhotoNpwp.root.setOnClickListener {
            currentPickingType = PickingType.NPWP
            imagePicker.launch("image/*")
        }
    }

    private fun setupStep3Listeners() {
        binding.step3.etBankName.addTextChangedListener { viewModel.processIntent(RegisterIntent.BankNameChanged(it.toString())) }
        binding.step3.etBankAccountNumber.addTextChangedListener { viewModel.processIntent(RegisterIntent.BankAccountNumberChanged(it.toString())) }
        binding.step3.etBankAccountHolder.addTextChangedListener { viewModel.processIntent(RegisterIntent.BankAccountHolderChanged(it.toString())) }
    }

    private fun setupStep4Listeners() {
        binding.step4.cbAgreement.setOnCheckedChangeListener { _, isChecked ->
            viewModel.processIntent(RegisterIntent.AgreementChanged(isChecked))
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.state.collectLatest { state ->
                        // Update Progress Stepper
                        binding.progressStepper.setProgress(state.progress, true)
                        binding.tvStepTitle.text = state.stepTitle

                        // Handle Step Visibility
                        binding.step1.root.visibility = if (state.currentStep == 1) View.VISIBLE else View.GONE
                        binding.step2.root.visibility = if (state.currentStep == 2) View.VISIBLE else View.GONE
                        binding.step3.root.visibility = if (state.currentStep == 3) View.VISIBLE else View.GONE
                        binding.step4.root.visibility = if (state.currentStep == 4) View.VISIBLE else View.GONE

                        // Handle Navigation Buttons
                        binding.btnBack.visibility = if (state.currentStep > 1) View.VISIBLE else View.INVISIBLE
                        binding.btnNext.text = if (state.currentStep == state.totalSteps) "Daftar" else "Lanjut"
                        
                        // Handle Loading State
                        binding.btnNext.isEnabled = !state.isLoading
                        binding.btnBack.isEnabled = !state.isLoading

                        state.error?.let {
                            showSnackbar(it, SnackbarType.ERROR)
                        }
                    }
                }

                launch {
                    viewModel.effect.collect { effect ->
                        when (effect) {
                            is RegisterEffect.NavigateToLogin -> {
                                findNavController().popBackStack()
                            }
                            is RegisterEffect.ShowError -> {
                                showSnackbar(effect.message, SnackbarType.ERROR)
                            }
                        }
                    }
                }
            }
        }
    }

    private fun uriToFile(uri: android.net.Uri): File {
        val inputStream = requireContext().contentResolver.openInputStream(uri)
        val file = File(requireContext().cacheDir, "temp_image_${System.currentTimeMillis()}.jpg")
        val outputStream = FileOutputStream(file)
        inputStream?.copyTo(outputStream)
        inputStream?.close()
        outputStream.close()
        return file
    }
}
