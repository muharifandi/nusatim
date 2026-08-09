package com.nusatim.partner.features.finance.ui.withdrawals.detail

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import coil.load
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.model.dto.WithdrawalResponse
import com.nusatim.partner.features.finance.databinding.FragmentWithdrawalDetailBinding
import com.nusatim.partner.features.finance.ui.withdrawals.WithdrawalsViewModel
import com.nusatim.partner.features.finance.ui.withdrawals.state.WithdrawalsIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch
import java.text.NumberFormat
import java.util.*

@AndroidEntryPoint
class WithdrawalDetailFragment : BaseFragment<FragmentWithdrawalDetailBinding>() {

    private val viewModel: WithdrawalsViewModel by viewModels()
    private val withdrawalId by lazy { arguments?.getInt("id") ?: -1 }

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.setNavigationOnClickListener {
            findNavController().navigateUp()
        }
        
        binding.layoutToolbar.toolbar.title = "Detail Penarikan"

        if (withdrawalId != -1) {
            viewModel.processIntent(WithdrawalsIntent.LoadDetail(withdrawalId))
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    state.selectedDetail?.let { renderDetail(it) }
                }
            }
        }
    }

    private fun renderDetail(item: WithdrawalResponse) {
        binding.tvAmount.text = formatRupiah(item.amount)
        binding.tvDate.text = getString(com.nusatim.partner.features.finance.R.string.withdrawal_label_requested_at, item.createdAt)
        
        binding.btnStatus.text = item.status?.replaceFirstChar { it.uppercase() } ?: "-"
        val statusColor = when (item.status?.lowercase()) {
            "pending" -> com.nusatim.partner.core.ui.R.color.status_warning
            "approved" -> com.nusatim.partner.core.ui.R.color.status_info
            "paid" -> com.nusatim.partner.core.ui.R.color.status_success
            "rejected" -> com.nusatim.partner.core.ui.R.color.partner_error
            else -> com.nusatim.partner.core.ui.R.color.status_neutral
        }
        binding.btnStatus.setBackgroundTintList(android.content.res.ColorStateList.valueOf(
            androidx.core.content.ContextCompat.getColor(requireContext(), statusColor)
        ))

        if (item.status == "rejected" && !item.rejectionReason.isNullOrEmpty()) {
            binding.cardRejection.visibility = android.view.View.VISIBLE
            binding.tvRejectionReason.text = item.rejectionReason
        } else {
            binding.cardRejection.visibility = android.view.View.GONE
        }

        binding.tvBankName.text = item.bankName
        binding.tvBankAccountNumber.text = item.bankAccountNumber
        binding.tvBankAccountHolder.text = item.bankAccountHolder

        binding.ivKtp.load(item.ktpUrl) {
            placeholder(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
            error(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
        }
        
        if (item.proofOfTransferUrl != null) {
            binding.ivProof.visibility = android.view.View.VISIBLE
            binding.tvProofPlaceholder.visibility = android.view.View.GONE
            binding.ivProof.load(item.proofOfTransferUrl) {
                placeholder(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
                error(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
            }
        } else {
            binding.ivProof.visibility = android.view.View.GONE
            binding.tvProofPlaceholder.visibility = android.view.View.VISIBLE
        }
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }
}
