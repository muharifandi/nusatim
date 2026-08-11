package com.nusatim.partner.features.finance.ui.withdrawals.form

import android.net.Uri
import androidx.activity.result.contract.ActivityResultContracts
import androidx.core.widget.addTextChangedListener
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import coil.load
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.common.security.SessionManager
import com.nusatim.partner.features.finance.databinding.FragmentWithdrawalFormBinding
import com.nusatim.partner.features.finance.ui.withdrawals.WithdrawalsViewModel
import com.nusatim.partner.features.finance.ui.withdrawals.state.WithdrawalsEffect
import com.nusatim.partner.features.finance.ui.withdrawals.state.WithdrawalsIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

import java.io.File
import java.io.FileOutputStream
import java.text.DecimalFormat
import java.text.DecimalFormatSymbols
import java.text.NumberFormat
import java.util.*
import javax.inject.Inject

@AndroidEntryPoint
class WithdrawalFormFragment : BaseFragment<FragmentWithdrawalFormBinding>() {

    @Inject
    lateinit var sessionManager: SessionManager

    private val viewModel: WithdrawalsViewModel by viewModels()
    private var selectedKtpFile: File? = null

    private val pickImage = registerForActivityResult(ActivityResultContracts.GetContent()) { uri: Uri? ->
        uri?.let { handleImagePicked(it) }
    }

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.setNavigationOnClickListener {
            findNavController().navigateUp()
        }

        binding.layoutToolbar.toolbar.title = getString(com.nusatim.partner.features.finance.R.string.withdrawal_form_title)

        binding.ivKtpPreview.setOnClickListener {
            pickImage.launch("image/*")
        }

        binding.etAmount.addTextChangedListener {
            val original = it.toString().replace("[^\\d]".toRegex(), "")
            if (original.isNotEmpty()) {
                val formatted = formatNominal(original.toLong())
                if (it.toString() != formatted) {
                    binding.etAmount.setText(formatted)
                    binding.etAmount.setSelection(formatted.length)
                }
            }
            validateForm()
        }

        binding.btnSubmit.setOnClickListener {
            val rawAmount = binding.etAmount.text.toString().replace("[^\\d]".toRegex(), "")
            val amount = rawAmount.toLongOrNull() ?: 0
            val ktpFile = selectedKtpFile
            val isKtpVerified = !viewModel.state.value.partnerProfile?.ktpUrl.isNullOrEmpty()

            if (isKtpVerified || ktpFile != null) {
                viewModel.processIntent(WithdrawalsIntent.SubmitRequest(amount, ktpFile ?: File(""), binding.etNote.text.toString()))
            }
        }

        val partnerName = sessionManager.getPartnerName() ?: "Partner"
        binding.tvBankInfo.text = getString(com.nusatim.partner.features.finance.R.string.withdrawal_info_bank_default, partnerName)
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.state.collect { state ->
                        state.balance?.let { balance ->
                            binding.tvAvailableBalance.text = formatRupiah(balance.availableBalance)
                        }

                        // Handle KTP Verified State
                        if (!state.partnerProfile?.ktpUrl.isNullOrEmpty()) {
                            binding.cardKtp.visibility = android.view.View.GONE
                        } else {
                            binding.cardKtp.visibility = android.view.View.VISIBLE
                        }

                        binding.btnSubmit.isEnabled = !state.isLoading && isFormValid()
                    }
                }
// ...

                launch {
                    viewModel.effect.collect { effect ->
                        when (effect) {
                            is WithdrawalsEffect.SuccessRequest -> {
                                findNavController().navigateUp()
                            }
                            is WithdrawalsEffect.ShowError -> {
                                binding.tilAmount.error = effect.message
                            }
                        }
                    }
                }
            }
        }
    }

    private fun handleImagePicked(uri: Uri) {
        val file = uriToFile(uri)
        selectedKtpFile = file
        binding.ivKtpPreview.load(file) {
            placeholder(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
            error(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
        }
        validateForm()
    }

    private fun validateForm() {
        binding.btnSubmit.isEnabled = isFormValid()
    }

    private fun isFormValid(): Boolean {
        val rawAmount = binding.etAmount.text.toString().replace("[^\\d]".toRegex(), "")
        val amount = rawAmount.toLongOrNull() ?: 0
        val balance = viewModel.state.value.balance
        val isKtpVerified = !viewModel.state.value.partnerProfile?.ktpUrl.isNullOrEmpty()

        return if (balance != null) {
            amount >= balance.minimumWithdrawal && amount <= balance.availableBalance && (isKtpVerified || selectedKtpFile != null)
        } else {
            false
        }
    }

    private fun formatNominal(amount: Long): String {
        val symbols = DecimalFormatSymbols(Locale("id", "ID")).apply {
            groupingSeparator = '.'
            decimalSeparator = ','
        }
        val formatter = DecimalFormat("#,###", symbols)
        return formatter.format(amount)
    }

    private fun uriToFile(uri: Uri): File {
        val inputStream = requireContext().contentResolver.openInputStream(uri)
        val file = File(requireContext().cacheDir, "temp_ktp_${System.currentTimeMillis()}.jpg")
        val outputStream = FileOutputStream(file)
        inputStream?.use { input ->
            outputStream.use { output ->
                input.copyTo(output)
            }
        }
        return file
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }
}
